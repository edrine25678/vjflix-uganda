@props(['series'])

@php
    $id = $series->slug ?: $series->id;
    $title = $series->title;
    $poster = $series->posterUrl();
    $vjName = $series->vj ? $series->vj->stage_name : 'VJ Translated';
    $year = $series->first_air_year ?: 2023;
    $rating = number_format($series->average_rating, 1);
    $seasonsCount = $series->seasons_count ?? $series->seasons->count();
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-xl bg-slate-900 border border-slate-800/80 hover:border-amber-500/50 transition-all duration-300 shadow-lg hover:shadow-amber-500/10 hover:-translate-y-1 w-52 sm:w-60 flex-shrink-0 mr-4">
    <!-- Poster Artwork Container -->
    <a href="{{ route('series.show', $id) }}" class="relative block aspect-[2/3] w-full overflow-hidden bg-slate-800">
        <img 
            src="{{ $poster }}" 
            alt="{{ $title }}" 
            loading="lazy"
            class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500">
        
        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent opacity-60 group-hover:opacity-40 transition-opacity"></div>

        <!-- Top Badges -->
        <div class="absolute top-2.5 left-2.5 flex flex-wrap gap-1.5 items-center">
            <span class="rounded bg-amber-500 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-black shadow-md">
                {{ $vjName }}
            </span>
            <span class="rounded bg-slate-950/80 backdrop-blur px-1.5 py-0.5 text-[9px] font-bold text-amber-300 border border-amber-500/30">
                SERIES
            </span>
        </div>

        <!-- Rating Pill -->
        <div class="absolute bottom-2.5 left-2.5 flex items-center space-x-1 rounded-md bg-slate-950/90 backdrop-blur px-2 py-1 text-[11px] font-bold text-amber-400 border border-slate-800">
            <x-bi-star-fill class="h-3 w-3 text-amber-400" />
            <span>{{ $rating }}</span>
        </div>
    </a>

    <!-- Metadata Body -->
    <div class="p-3 flex flex-col flex-grow justify-between">
        <div>
            <a href="{{ route('series.show', $id) }}" class="block">
                <h3 class="font-bold text-sm text-slate-100 group-hover:text-amber-400 transition-colors line-clamp-1" title="{{ $title }}">
                    {{ $title }}
                </h3>
            </a>
            <div class="mt-1 flex items-center space-x-2 text-[11px] text-slate-400">
                <span>{{ $year }}</span>
                <span>•</span>
                <span>{{ $seasonsCount }} {{ Str::plural('Season', $seasonsCount) }}</span>
                <span>•</span>
                <span class="text-amber-400 font-medium">Luganda</span>
            </div>
        </div>

        <!-- Quick Action Play Button -->
        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
            <a href="{{ route('series.show', $id) }}" class="inline-flex items-center text-xs font-semibold text-slate-300 group-hover:text-white transition-colors">
                <x-bi-play-fill class="h-4 w-4 mr-1 text-amber-400" />
                <span>Episodes</span>
            </a>
            <span class="text-[10px] text-slate-500 uppercase tracking-wider font-semibold">HD Dubbed</span>
        </div>
    </div>
</div>
