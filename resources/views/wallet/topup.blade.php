<x-layout>
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="border-b border-slate-700 pb-4">
            <h1 class="text-3xl font-extrabold text-white">طلب شحن رصيد</h1>
            <p class="text-indigo-400 mt-1 text-sm">حوّل المبلغ إلى أحد الحسابات التالية، ثم أرفق صورة إيصال التحويل</p>
        </div>

        @if ($bankAccounts->isEmpty())
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-lg p-4 text-sm">
                لا توجد حسابات بنكية متاحة حالياً. يرجى المحاولة لاحقاً.
            </div>
        @else
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <h2 class="text-lg font-bold text-white mb-4">الحسابات البنكية المتاحة</h2>
                <div class="space-y-3">
                    @foreach ($bankAccounts as $account)
                        <div class="border border-slate-700 rounded-lg p-4 text-sm text-slate-300">
                            <p><span class="text-slate-500">البنك:</span> {{ $account->bank_name }}</p>
                            <p><span class="text-slate-500">اسم صاحب الحساب:</span> {{ $account->account_holder_name }}</p>
                            <p><span class="text-slate-500">رقم الحساب:</span> {{ $account->account_number }}</p>
                            @if ($account->iban)
                                <p><span class="text-slate-500">IBAN:</span> {{ $account->iban }}</p>
                            @endif
                            @if ($account->notes)
                                <p class="text-slate-500 mt-1">{{ $account->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <form method="POST" action="{{ route('wallet.topup.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label for="bank_account_id" class="block font-medium text-sm text-slate-300">التحويل إلى حساب</label>
                        <select id="bank_account_id" name="bank_account_id" required
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            @foreach ($bankAccounts as $account)
                                <option value="{{ $account->id }}" @selected(old('bank_account_id') == $account->id)>
                                    {{ $account->bank_name }} - {{ $account->account_number }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('bank_account_id')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="amount" class="block font-medium text-sm text-slate-300">المبلغ المحوّل (ج.س)</label>
                        <input id="amount" type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" required
                            class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <x-input-error :messages="$errors->get('amount')" class="mt-1 !text-red-400" />
                    </div>

                    <div>
                        <label for="receipt" class="block font-medium text-sm text-slate-300">صورة إيصال التحويل</label>
                        <input id="receipt" type="file" name="receipt" accept="image/png,image/jpeg,image/webp" required
                            class="mt-1 block w-full text-sm text-slate-300 file:me-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 rounded-md bg-slate-900 border border-slate-700">
                        <p class="text-slate-500 text-xs mt-1">الصيغ المقبولة: JPG, PNG, WEBP - الحجم الأقصى 5 ميغابايت</p>
                        <x-input-error :messages="$errors->get('receipt')" class="mt-1 !text-red-400" />
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-md transition">
                        إرسال طلب الشحن
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-layout>