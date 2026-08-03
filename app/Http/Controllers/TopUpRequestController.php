<?php

namespace App\Http\Controllers;

use App\Enums\TopUpRequestStatus;
use App\Enums\UserRole;
use App\Models\BankAccount;
use App\Models\TopUpRequest;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'receipt' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
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

        Notification::make()
            ->title('طلب شحن محفظة جديد')
            ->body("قدّم {$user->name} طلب شحن بمبلغ " . number_format((float) $topUpRequest->amount, 2) . ' ج.س، بانتظار المراجعة.')
            ->info()
            ->sendToDatabase($admins);

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
