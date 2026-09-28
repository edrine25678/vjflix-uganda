@props(['movies', 'category' => 'Featured Movies'])

<section class="my-8">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
            <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
            {{ $category }}
        </h2>
    </div>

    <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
        @forelse ($movies as $movie)
            <x-vjflix-card :movie="$movie" />
        @empty
            <div class="text-xs text-slate-500 italic py-4">No translations available in this section yet.</div>
        @endforelse
    </div>
</section>
