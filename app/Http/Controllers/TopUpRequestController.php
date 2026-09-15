<?php

namespace App\Http\Controllers;

use App\Enums\TopUpRequestStatus;
use App\Enums\UserRole;
use App\Models\BankAccount;
use App\Models\TopUpRequest;
use App\Models\User;
use App\Notifications\TopUpRequestCreated;
use Filament\Notifications\Events\DatabaseNotificationsSent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TopUpRequestController extends Controller
{
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($this->hasPendingRequest($user)) {
            return redirect()->route('wallet.index')
                ->with('status', 'لديك بالفعل طلب شحن قيد المراجعة. يرجى الانتظار حتى تتم مراجعته قبل إرسال طلب جديد.');
        }

        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('wallet.topup', ['bankAccounts' => $bankAccounts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($this->hasPendingRequest($user)) {
            return redirect()->route('wallet.index')
                ->with('status', 'لديك بالفعل طلب شحن قيد المراجعة.');
        }

        $validated = $request->validate([
            'bank_account_id' => [
                'required',
                // Previously this only checked the ID existed at all, so a
                // crafted POST could reference a bank account that's been
                // deactivated and no longer shows in the form. Now it must
                // also be active, matching what create() actually displays.
                Rule::exists('bank_accounts', 'id')->where('is_active', true),
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'bank_account_id.exists' => 'الحساب البنكي المحدد غير متاح حالياً.',
            'amount.min' => 'يجب أن يكون المبلغ أكبر من صفر.',
            'receipt.required' => 'يرجى إرفاق صورة إيصال التحويل.',
            'receipt.image' => 'يجب أن يكون الملف المرفق صورة.',
            'receipt.mimes' => 'الصيغ المقبولة هي JPG وPNG وWEBP فقط.',
            'receipt.max' => 'يجب ألا يتجاوز حجم الصورة 5 ميغابايت.',
        ]);

        $path = $request->file('receipt')->store("receipts/{$user->id}", 'public');

        $topUpRequest = TopUpRequest::create([
            'user_id' => $user->id,
            'bank_account_id' => $validated['bank_account_id'],
            'amount' => $validated['amount'],
            'receipt_path' => $path,
            'status' => TopUpRequestStatus::Pending,
        ]);

        $admins = User::where('role', UserRole::Admin)->get();

        // Sync (non-queued) notification so admins see it immediately without
        // running `php artisan queue:work`. Filament's own Notification class
        // implements ShouldQueue and was getting stuck in the `jobs` table.
        // Broadcast is best-effort: the DB row must persist even when Reverb
        // is down, so failures there must not break the top-up submission.
        foreach ($admins as $admin) {
            try {
                $admin->notify(new TopUpRequestCreated(
                    topUpRequest: $topUpRequest,
                    title: 'طلب شحن محفظة جديد',
                    body: "قدّم {$user->name} طلب شحن بمبلغ " . number_format((float) $topUpRequest->amount, 2) . ' ج.س، بانتظار المراجعة.',
                ));
            } catch (\Throwable $e) {
                report($e);
            }

            // Triggers Filament admin bell live refresh
            // (`.database-notifications.sent`) when Reverb is running.
            // Polling every 30s is the fallback when it is not.
            try {
                DatabaseNotificationsSent::dispatch($admin);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return redirect()->route('wallet.index')
            ->with('status', 'تم إرسال طلب الشحن بنجاح، وسيتم مراجعته من قبل الإدارة قريباً.');
    }

    private function hasPendingRequest(User $user): bool
    {
        return $user->topUpRequests()
            ->where('status', TopUpRequestStatus::Pending)
            ->exists();
    }
}
