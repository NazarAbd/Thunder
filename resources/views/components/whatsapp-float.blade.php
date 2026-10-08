@php($wa = config('store.whatsapp'))
@if(!empty($wa))
    {{-- Global WhatsApp shortcut: fixed corner on every page using <x-layout/>. --}}
    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" aria-label="تواصل واتساب"
       class="fixed bottom-5 left-5 z-50 w-14 h-14 rounded-full bg-[#25D366] hover:bg-[#1eb856] text-white flex items-center justify-center text-3xl shadow-2xl shadow-emerald-500/30 transition hover:scale-110">
        <i class="fa-brands fa-whatsapp"></i>
    </a>
@endif
