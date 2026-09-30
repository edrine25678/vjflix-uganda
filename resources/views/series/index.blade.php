<x-layout>
    <x-slot:title>Translated TV Series — VJFlix Uganda</x-slot:title>

    <x-header />

    <!-- Hero Series Banner -->
    @if ($heroSeries)
        <div class="relative min-h-[500px] md:min-h-[580px] w-full overflow-hidden bg-slate-950 flex items-center">
            <!-- Backdrop Image -->
            <div class="absolute inset-0">
                <img src="{{ $heroSeries->backdropUrl() }}" alt="{{ $heroSeries->title }}" class="h-full w-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 w-full">
                <div class="max-w-2xl">
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if ($heroSeries->vj)
                            <a href="/vjs/{{ $heroSeries->vj->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 hover:bg-amber-400 text-black px-3.5 py-1 text-xs font-extrabold uppercase tracking-wide shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                                <x-bi-mic-fill class="h-3 w-3" />
                                Voiced by {{ $heroSeries->vj->stage_name }}
                            </a>
                        @endif
                        <span class="rounded bg-amber-500/20 border border-amber-500/40 px-2 py-0.5 text-xs font-bold text-amber-300 uppercase">
                            Featured Series
                        </span>
                        <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-slate-300">
                            {{ $heroSeries->first_air_year }}
                        </span>
                        <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-amber-400">
                            ★ {{ number_format($heroSeries->average_rating, 1) }}
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-none font-display">
                        {{ $heroSeries->title }}
                    </h1>

                    <p class="mt-4 text-sm sm:text-base text-slate-300 line-clamp-3 leading-relaxed">
                        {{ $heroSeries->synopsis ?: $heroSeries->description }}
                    </p>

                    <!-- CTAs -->
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('series.show', $heroSeries->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-3.5 text-sm font-extrabold text-black shadow-xl shadow-amber-500/20 hover:from-amber-400 hover:to-yellow-400 transition-all hover:scale-105">
                            <x-bi-play-fill class="h-6 w-6 mr-1.5" />
                            START WATCHING
                        </a>

                        <a href="{{ route('series.show', $heroSeries->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700 px-5 py-3.5 text-sm font-bold text-slate-200 backdrop-blur transition-all">
                            <x-bi-list-ul class="h-4 w-4 mr-2 text-amber-400" />
                            View Seasons & Episodes
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-10">
        <!-- VJ Filter Pills -->
        @if ($seriesVjs->isNotEmpty())
            <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
                <a href="{{ route('series.index') }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all whitespace-nowrap {{ !$selectedVj ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700' }}">
                    All Series
                </a>
                @foreach ($seriesVjs as $vj)
                    <a href="{{ route('series.index', ['vj' => $vj->slug]) }}" class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-all whitespace-nowrap {{ $selectedVj === $vj->slug ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        {{ $vj->stage_name }} ({{ $vj->series_count }})
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Trending Series Row -->
        @if ($trendingSeries->isNotEmpty() && !$selectedGenre && !$selectedVj)
            <section class="my-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        Trending TV Series in Uganda 🔥
                    </h2>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
                    @foreach ($trendingSeries as $tSeries)
                        <x-series-card :series="$tSeries" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- All Series Grid -->
        <section class="my-8">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-6 rounded-full bg-amber-500 inline-block"></span>
                        {{ $selectedVj ? 'Series Translated by VJ' : ($selectedGenre ? ucfirst($selectedGenre) . ' Series' : 'Explore All Translated Series') }}
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Stream full seasons translated into Luganda by Uganda's elite commentators</p>
                </div>

                <!-- Genre Filters -->
                <div class="flex flex-wrap gap-1.5">
                    @foreach ($genres as $slug => $name)
                        <a href="{{ route('series.index', ['genre' => $slug]) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-all {{ $selectedGenre === $slug ? 'bg-amber-500 text-black' : 'bg-slate-800 text-slate-300 hover:text-white' }}">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>

            @if ($allSeries->isNotEmpty())
                <div class="grid grid-cols-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2 sm:gap-4 lg:gap-6">
                    @foreach ($allSeries as $sItem)
                        <div class="w-full">
                            <x-series-card :series="$sItem" class="!w-full !max-w-none !mr-0" />
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $allSeries->links() }}
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center text-slate-400">
                    <x-bi-tv class="mx-auto h-12 w-12 text-slate-600 mb-3" />
                    <h3 class="text-base font-bold text-slate-200">No series found matching your criteria</h3>
                    <p class="text-xs text-slate-500 mt-1">Try clearing filters or search for another title.</p>
                </div>
            @endif
        </section>
    </main>

    <x-footer />
</x-layout>
