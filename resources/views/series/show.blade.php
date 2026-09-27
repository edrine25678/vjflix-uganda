<x-layout>
    <x-slot:title>{{ $series->title }} (Full Series) — VJFlix Uganda</x-slot:title>

    <x-header />

    <!-- Series Showcase Hero -->
    <div class="relative w-full overflow-hidden bg-slate-950">
        <!-- Backdrop Banner -->
        <div class="relative h-[380px] sm:h-[480px] w-full overflow-hidden">
            <img src="{{ $series->backdropUrl() }}" alt="{{ $series->title }}" class="h-full w-full object-cover opacity-35 filter blur-[1px]">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
        </div>

        <!-- Poster & Title Metadata Block -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-48 sm:-mt-64 pb-8 flex flex-col md:flex-row items-center md:items-start gap-8 text-center md:text-left">
                <!-- Series Poster Container -->
                <div class="relative flex-shrink-0 w-48 sm:w-64 rounded-2xl overflow-hidden shadow-2xl ring-4 ring-slate-800 bg-slate-900 aspect-[2/3]">
                    <img src="{{ $series->posterUrl() }}" alt="{{ $series->title }}" class="h-full w-full object-cover">
                    <span class="absolute top-2.5 right-2.5 rounded bg-amber-500 px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-black shadow-lg">
                        SERIES
                    </span>
                </div>

                <!-- Info Column -->
                <div class="flex-grow max-w-3xl">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-3">
                        @if ($series->vj)
                            <a href="/vjs/{{ $series->vj->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 hover:bg-amber-400 text-black px-3.5 py-1 text-xs font-extrabold uppercase tracking-wide shadow-md transition-all hover:scale-105">
                                <x-bi-mic-fill class="h-3 w-3" />
                                Voiced by {{ $series->vj->stage_name }}
                            </a>
                        @endif

                        <span class="rounded bg-slate-800/80 px-2.5 py-1 text-xs font-bold text-slate-300 border border-slate-700">
                            {{ $series->first_air_year }}
                        </span>

                        <span class="rounded bg-slate-800/80 px-2.5 py-1 text-xs font-bold text-amber-400 border border-slate-700 flex items-center gap-1">
                            <x-bi-star-fill class="h-3 w-3 text-amber-400" />
                            {{ number_format($series->average_rating, 1) }}
                        </span>

                        <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2.5 py-1 text-xs font-bold text-amber-400">
                            {{ $series->seasons->count() }} {{ Str::plural('Season', $series->seasons->count()) }}
                        </span>

                        <span class="rounded bg-slate-800 px-2 py-0.5 text-xs text-slate-400">
                            {{ number_format($series->views) }} views
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight font-display">
                        {{ $series->title }}
                    </h1>

                    <!-- Genre tags -->
                    @if ($series->genres->isNotEmpty())
                        <div class="mt-3 flex flex-wrap items-center justify-center md:justify-start gap-1.5">
                            @foreach ($series->genres as $genre)
                                <span class="rounded-md bg-slate-900 border border-slate-800 px-2 py-0.5 text-[11px] font-medium text-slate-300">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <p class="mt-4 text-sm sm:text-base text-slate-300 leading-relaxed">
                        {{ $series->description ?: $series->synopsis }}
                    </p>

                    <!-- Quick Start Button -->
                    @if ($firstEpisode)
                        <div class="mt-6 flex flex-wrap items-center justify-center md:justify-start gap-4">
                            <a href="{{ route('series.watch', [$series->slug, $series->seasons->first()->season_number, $firstEpisode->episode_number]) }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-3.5 text-sm font-extrabold text-black shadow-xl shadow-amber-500/20 hover:from-amber-400 hover:to-yellow-400 transition-all hover:scale-105">
                                <x-bi-play-fill class="h-6 w-6 mr-1.5" />
                                WATCH EPISODE 1 (S1 E1)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Seasons & Episodes Section -->
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-12">
        <section x-data="{ activeSeason: {{ $series->seasons->first() ? $series->seasons->first()->id : 'null' }} }">
            <div class="border-b border-slate-800 pb-4 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-full bg-amber-500 inline-block"></span>
                        Seasons & Episodes
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Select a season to view and stream translated episodes</p>
                </div>

                <!-- Season selector pills -->
                @if ($series->seasons->count() > 1)
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
                        @foreach ($series->seasons as $season)
                            <button 
                                @click="activeSeason = {{ $season->id }}" 
                                type="button" 
                                :class="activeSeason === {{ $season->id }} ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-900 border border-slate-800 text-slate-300 hover:bg-slate-800'"
                                class="px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
                                {{ $season->title ?: 'Season ' . $season->season_number }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Season Content Panels -->
            @foreach ($series->seasons as $season)
                <div x-show="activeSeason === {{ $season->id }}" x-cloak class="space-y-4">
                    @if ($season->overview)
                        <div class="rounded-xl bg-slate-900/60 border border-slate-800/80 p-4 text-xs text-slate-400 mb-6">
                            <span class="font-bold text-slate-200 mr-2">{{ $season->title ?: 'Season ' . $season->season_number }}:</span>
                            {{ $season->overview }}
                        </div>
                    @endif

                    @if ($season->episodes->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($season->episodes as $episode)
                                <div class="group relative flex flex-col sm:flex-row overflow-hidden rounded-xl bg-slate-900/90 border border-slate-800 hover:border-amber-500/60 transition-all duration-300 shadow-md hover:shadow-amber-500/10">
                                    <!-- Episode Thumbnail -->
                                    <a href="{{ route('series.watch', [$series->slug, $season->season_number, $episode->episode_number]) }}" class="relative sm:w-48 aspect-video flex-shrink-0 bg-slate-800 overflow-hidden block">
                                        <img src="{{ $episode->thumbnailUrl() }}" alt="{{ $episode->title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <div class="rounded-full bg-amber-500 p-2.5 text-black shadow-lg">
                                                <x-bi-play-fill class="h-5 w-5" />
                                            </div>
                                        </div>
                                        <span class="absolute bottom-1.5 right-1.5 rounded bg-black/80 backdrop-blur px-1.5 py-0.5 text-[10px] font-bold text-slate-300">
                                            {{ $episode->durationFormatted() }}
                                        </span>
                                    </a>

                                    <!-- Episode Details -->
                                    <div class="p-4 flex flex-col justify-between flex-grow">
                                        <div>
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <span class="text-[11px] font-extrabold text-amber-400 uppercase tracking-wider">
                                                    Episode {{ $episode->episode_number }}
                                                </span>
                                                @if ($episode->is_free)
                                                    <span class="rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-1.5 py-0.5 text-[10px] font-bold">
                                                        FREE
                                                    </span>
                                                @endif
                                            </div>

                                            <a href="{{ route('series.watch', [$series->slug, $season->season_number, $episode->episode_number]) }}" class="font-bold text-sm text-white group-hover:text-amber-400 transition-colors line-clamp-1">
                                                {{ $episode->title }}
                                            </a>

                                            <p class="mt-1.5 text-xs text-slate-400 line-clamp-2 leading-relaxed">
                                                {{ $episode->overview ?: 'Official Luganda translated episode voiced by ' . ($episode->vj ? $episode->vj->stage_name : ($series->vj ? $series->vj->stage_name : 'Ugandan VJ')) }}
                                            </p>
                                        </div>

                                        <div class="mt-3 pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                            <span class="text-[10px] text-slate-500">{{ number_format($episode->views) }} views</span>
                                            <a href="{{ route('series.watch', [$series->slug, $season->season_number, $episode->episode_number]) }}" class="inline-flex items-center text-xs font-bold text-amber-400 hover:text-amber-300">
                                                Stream Now &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-800 p-8 text-center text-slate-500 text-xs">
                            No episodes uploaded for this season yet.
                        </div>
                    @endif
                </div>
            @endforeach
        </section>

        <!-- More Series translated by this VJ -->
        @if ($moreByVj->isNotEmpty() && $series->vj)
            <section class="mt-12 pt-8 border-t border-slate-800">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg md:text-xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        More Series Translated by {{ $series->vj->stage_name }}
                    </h3>
                    <a href="/vjs/{{ $series->vj->slug }}" class="text-xs font-bold text-amber-400 hover:underline">
                        View VJ Profile &rarr;
                    </a>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
                    @foreach ($moreByVj as $otherSeries)
                        <x-series-card :series="$otherSeries" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-footer />
</x-layout>
