<x-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        <h1 class="text-3xl font-extrabold text-white">{{ __('Profile') }}</h1>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10">
            @include('profile.partials.update-password-form')
        </div>

        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 sm:p-10">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-layout>