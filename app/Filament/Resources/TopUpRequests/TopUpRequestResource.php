<?php

namespace App\Filament\Resources\TopUpRequests;

use App\Enums\TopUpRequestStatus;
use App\Filament\Resources\TopUpRequests\Pages;
use App\Models\TopUpRequest;
use App\Notifications\TopUpRequestReviewed;
use App\Services\WalletService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TopUpRequestResource extends Resource
{
    protected static ?string $model = TopUpRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'طلبات الشحن';

    protected static ?string $modelLabel = 'طلب شحن';

    protected static ?string $pluralModelLabel = 'طلبات الشحن';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * Resolve a public URL for the transfer receipt, or null when there is
     * nothing to preview (no path stored, or the file was deleted from disk).
     * The receipt-preview view renders a placeholder in that case instead of
     * crashing the approve/reject confirmation modal.
     */
    private static function receiptUrl(TopUpRequest $record): ?string
    {
        if (empty($record->receipt_path)) {
            return null;
        }

        if (! Storage::disk('public')->exists($record->receipt_path)) {
            return null;
        }

        return Storage::disk('public')->url($record->receipt_path);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('المستخدم')->searchable(),
                TextColumn::make('bankAccount.bank_name')->label('البنك'),
                TextColumn::make('amount')->label('المبلغ')->money('SDG')->sortable(),
                ImageColumn::make('receipt_path')
                    ->label('الإيصال')
                    ->disk('public')
                    ->height(60)
                    ->width(60),
                TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(TopUpRequestStatus $state): string => match ($state) {
                        TopUpRequestStatus::Pending => 'warning',
                        TopUpRequestStatus::Approved => 'success',
                        TopUpRequestStatus::Rejected => 'danger',
                    })
                    ->formatStateUsing(fn(TopUpRequestStatus $state): string => $state->label()),
                TextColumn::make('rejection_reason')->label('سبب الرفض')->limit(40)->toggleable(),
                TextColumn::make('created_at')->label('تاريخ الطلب')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            // Blue-square highlight for the row deep-linked from a notification
            // (?highlight={id}). Never marks anything as read — purely visual.
            ->recordClasses(fn ($record): array => (
                (string) request()->query('highlight', '') !== ''
                && (string) request()->query('highlight') === (string) $record->getKey()
                    ? ['topup-highlight-row', 'ring-2', 'ring-blue-500', 'bg-blue-500/10']
                    : []
            ))
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        TopUpRequestStatus::Pending->value => 'قيد المراجعة',
                        TopUpRequestStatus::Approved->value => 'مقبول',
                        TopUpRequestStatus::Rejected->value => 'مرفوض',
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('قبول')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(TopUpRequest $record): bool => $record->status === TopUpRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->modalWidth('lg')
                    ->modalHeading('تأكيد قبول طلب الشحن')
                    ->modalDescription('سيتم إضافة المبلغ إلى محفظة المستخدم فوراً. هل أنت متأكد؟')
                    ->schema([
                        View::make('filament.schemas.receipt-preview')
                            ->viewData(fn ($record): array => [
                                'url' => $record instanceof TopUpRequest ? self::receiptUrl($record) : null,
                            ]),
                    ])
                    ->action(function (TopUpRequest $record): void {
                        $reviewerId = auth()->id();

                        $wasProcessed = DB::transaction(function () use ($record, $reviewerId) {
                            $locked = TopUpRequest::where('id', $record->id)
                                ->lockForUpdate()
                                ->first();

                            if (! $locked || $locked->status !== TopUpRequestStatus::Pending) {
                                return false;
                            }

                            // The request row survives on restrict, but the user itself
                            // may be gone (or the relation otherwise unresolvable).
                            // Skip the credit rather than calling credit() on null.
                            $user = $locked->user;

                            if (! $user) {
                                return false;
                            }

                            app(WalletService::class)->credit(
                                $user,
                                (float) $locked->amount,
                                'شحن محفظة عبر تحويل بنكي',
                                $locked,
                            );

                            $locked->update([
                                'status' => TopUpRequestStatus::Approved,
                                'reviewed_by' => $reviewerId,
                                'reviewed_at' => now(),
                            ]);

                            return true;
                        });

                        if (! $wasProcessed) {
                            Notification::make()
                                ->title('تم التعامل مع هذا الطلب مسبقاً')
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->refresh();

                        // Notify only when the user still exists; the status update
                        // above is already committed and must not fail because of this.
                        $user = $record->user;

                        if ($user) {
                            $user->notify(new TopUpRequestReviewed(
                                topUpRequest: $record,
                                title: 'تم قبول طلب الشحن',
                                body: 'تم شحن محفظتك بمبلغ ' . number_format((float) $record->amount, 2) . ' ج.س بنجاح.',
                            ));
                        }
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(TopUpRequest $record): bool => $record->status === TopUpRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->modalWidth('lg')
                    ->modalHeading('رفض طلب الشحن')
                    ->schema([
                        View::make('filament.schemas.receipt-preview')
                            ->viewData(fn ($record): array => [
                                'url' => $record instanceof TopUpRequest ? self::receiptUrl($record) : null,
                            ]),
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (TopUpRequest $record, array $data): void {
                        $reviewerId = auth()->id();

                        $wasProcessed = DB::transaction(function () use ($record, $data, $reviewerId) {
                            $locked = TopUpRequest::where('id', $record->id)
                                ->lockForUpdate()
                                ->first();

                            if (! $locked || $locked->status !== TopUpRequestStatus::Pending) {
                                return false;
                            }

                            $locked->update([
                                'status' => TopUpRequestStatus::Rejected,
                                'rejection_reason' => $data['rejection_reason'],
                                'reviewed_by' => $reviewerId,
                                'reviewed_at' => now(),
                            ]);

                            return true;
                        });

                        if (! $wasProcessed) {
                            Notification::make()
                                ->title('تم التعامل مع هذا الطلب مسبقاً')
                                ->warning()
                                ->send();

                            return;
                        }

                        $record->refresh();

                        // Same guard as approve: rejection is committed, the
                        // notification is best-effort when the user still exists.
                        $user = $record->user;

                        if ($user) {
                            $user->notify(new TopUpRequestReviewed(
                                topUpRequest: $record,
                                title: 'تم رفض طلب الشحن',
                                body: 'سبب الرفض: ' . $data['rejection_reason'],
                            ));
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTopUpRequests::route('/'),
        ];
    }
}
