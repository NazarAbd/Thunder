<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-slate-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block font-medium text-sm text-slate-300">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password" class="block font-medium text-sm text-slate-300">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block font-medium text-sm text-slate-300">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                class="mt-1 block w-full rounded-md bg-slate-900 border-slate-700 text-slate-100 placeholder-slate-500 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>