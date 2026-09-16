<x-layout>
    <div class="flex items-center justify-center min-h-[50vh]">
        <div class="w-full max-w-md">
            <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-8">
                <div class="mb-4 text-sm text-slate-300">
        {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
            </div>
        </div>
    </div>
</x-layout>
