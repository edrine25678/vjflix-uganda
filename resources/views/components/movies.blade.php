@props(['movies', 'category' => 'Featured Movies', 'seeMoreUrl' => null])

@php
    $url = $seeMoreUrl ?: route('vjflix.index');
    $displayMovies = $movies->take(9);
@endphp

<section class="my-6 sm:my-8">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-base sm:text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
            <span class="w-1.5 h-4 sm:h-5 rounded-full bg-amber-500 inline-block"></span>
            {{ $category }}
        </h2>
        <a href="{{ $url }}" class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center gap-1">
            <span>See All</span>
            <x-bi-chevron-right class="h-3 w-3" />
        </a>
    </div>

    <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
        @forelse ($displayMovies as $movie)
            <x-vjflix-card :movie="$movie" />
        @empty
            <div class="text-xs text-slate-500 italic py-4">No translations available in this section yet.</div>
        @endforelse

        @if ($movies->isNotEmpty())
            <!-- 10th Card: See More / Explore All Card -->
            <a href="{{ $url }}" 
               class="group relative flex flex-col items-center justify-center text-center overflow-hidden rounded-lg sm:rounded-xl bg-gradient-to-b from-slate-900 via-slate-900/90 to-slate-950 border border-slate-800 hover:border-amber-500/80 transition-all duration-300 shadow-md sm:shadow-lg hover:shadow-amber-500/20 hover:-translate-y-1 w-[21vw] min-w-[72px] max-w-[90px] sm:min-w-0 sm:max-w-none sm:w-44 md:w-52 lg:w-60 flex-shrink-0 mr-1.5 sm:mr-4 p-2 sm:p-5">
                <div class="h-8 w-8 sm:h-14 sm:w-14 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-black group-hover:scale-110 transition-all duration-300 shadow-lg mb-2 sm:mb-3">
                    <x-bi-arrow-right class="h-4 w-4 sm:h-6 sm:w-6" />
                </div>
                <h3 class="font-bold text-[10px] sm:text-sm text-white group-hover:text-amber-400 transition-colors leading-tight">
                    See More
                </h3>
                <p class="hidden sm:block text-[11px] text-slate-400 mt-1 line-clamp-1">
                    {{ $category }}
                </p>
                <span class="mt-1.5 sm:mt-3 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded bg-slate-800 group-hover:bg-amber-500/20 text-[8px] sm:text-[10px] font-bold text-amber-400 border border-slate-700 group-hover:border-amber-500/30 transition-colors">
                    Explore &rarr;
                </span>
            </a>
        @endif
    </div>
</section>
