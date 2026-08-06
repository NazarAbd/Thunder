<?php

namespace App\Filament\Resources\TopUpRequests;

use App\Enums\TopUpRequestStatus;
use App\Filament\Resources\TopUpRequests\Pages;
use App\Models\TopUpRequest;
use App\Services\WalletService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TopUpRequestResource extends Resource
{
    protected static ?string $model = TopUpRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'طلبات الشحن';

    protected static ?string $modelLabel = 'طلب شحن';

    protected static ?string $pluralModelLabel = 'طلبات الشحن';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
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
                    ->modalHeading('تأكيد قبول طلب الشحن')
                    ->modalDescription('سيتم إضافة المبلغ إلى محفظة المستخدم فوراً. هل أنت متأكد؟')
                    ->action(function (TopUpRequest $record): void {
                        app(WalletService::class)->credit(
                            $record->user,
                            (float) $record->amount,
                            'شحن محفظة عبر تحويل بنكي',
                            $record,
                        );

                        $record->update([
                            'status' => TopUpRequestStatus::Approved,
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('تم قبول طلب الشحن')
                            ->body('تم شحن محفظتك بمبلغ ' . number_format((float) $record->amount, 2) . ' ج.س بنجاح.')
                            ->success()
                            ->sendToDatabase($record->user);
                    }),

                Action::make('reject')
                    ->label('رفض')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(TopUpRequest $record): bool => $record->status === TopUpRequestStatus::Pending)
                    ->requiresConfirmation()
                    ->modalHeading('رفض طلب الشحن')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('سبب الرفض')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (TopUpRequest $record, array $data): void {
                        $record->update([
                            'status' => TopUpRequestStatus::Rejected,
                            'rejection_reason' => $data['rejection_reason'],
                            'reviewed_by' => auth()->id(),
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('تم رفض طلب الشحن')
                            ->body('سبب الرفض: ' . $data['rejection_reason'])
                            ->danger()
                            ->sendToDatabase($record->user);
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
