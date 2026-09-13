<div x-data="{ open: false }">
    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">صورة إيصال التحويل</p>

    {{-- When there is no receipt file (never uploaded, or deleted from disk),
         show a placeholder instead of rendering a broken image / crashing. --}}
    @if (empty($url ?? null))
        <p class="text-sm text-gray-500 dark:text-gray-400 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center">لا توجد صورة إيصال متاحة لهذا الطلب.</p>
    @else
        {{-- Thumbnail: tapping it opens the full-size overlay below --}}
        <button
            type="button"
            @click="open = true"
            class="block rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600"
        >
            <img src="{{ $url }}" alt="صورة الإيصال" class="w-32 h-32 object-cover" />
        </button>

        {{-- Full-size overlay. @click on the dark backdrop closes it; @click.stop on
             the image itself stops that same click from bubbling up and closing it,
             so only taps OUTSIDE the image (anywhere on the backdrop) close it. --}}
        <div
            x-show="open"
            @click="open = false"
            class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 p-6"
            style="display: none;"
        >
            <img
                src="{{ $url }}"
                alt="صورة الإيصال (حجم كامل)"
                @click.stop
                class="max-w-full max-h-full rounded-lg shadow-2xl"
            />
        </div>
    @endif
</div>