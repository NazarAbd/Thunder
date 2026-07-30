@props(['name', 'image', 'description' => '', 'href' => '#'])

<div class="bg-slate-800 border border-slate-700 rounded-xl overflow-hidden hover:border-indigo-500 transition duration-300 flex flex-col">
    <img src="{{ $image }}" alt="{{ $name }}" class="h-40 w-full object-cover">

    <div class="p-5 flex flex-col flex-grow">
        <h3 class="font-bold text-lg text-white mb-2">{{ $name }}</h3>
        <p class="text-slate-400 text-sm mb-4 flex-grow">{{ $description }}</p>

        <a href="{{ $href }}"
           class="w-full block text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition">
            اطلب الآن
        </a>
    </div>
</div>