<x-layout>
    <x-slot:title>{{ $movie->title }} (Translated by {{ $movie->vj ? $movie->vj->stage_name : 'Ugandan VJ' }}) — VJFlix Uganda</x-slot:title>

    <x-header />

    <!-- Hero Backdrop Banner -->
    <div class="relative min-h-[500px] w-full overflow-hidden bg-slate-950 border-b border-slate-800">
        <!-- Backdrop Image -->
        <div class="absolute inset-0">
            <img src="{{ $movie->backdropUrl() }}" alt="{{ $movie->title }}" class="h-full w-full object-cover object-center opacity-30 filter blur-sm scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/80 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/70 to-transparent"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8 items-center md:items-start">
                <!-- Poster Card -->
                <div class="w-56 sm:w-64 flex-shrink-0 overflow-hidden rounded-2xl bg-slate-900 border-2 border-amber-500/40 shadow-2xl shadow-amber-500/10">
                    <img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" class="h-full w-full object-cover">
                </div>

                <!-- Movie Details Header -->
                <div class="flex-grow text-center md:text-left">
                    <!-- Badges -->
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-3">
                        @if ($movie->vj)
                            <a href="/vjs/{{ $movie->vj->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 hover:bg-amber-400 text-black px-3 py-1 text-xs font-extrabold uppercase tracking-wide shadow-md transition-all">
                                <x-bi-mic-fill class="h-3 w-3" />
                                Translated by {{ $movie->vj->stage_name }}
                            </a>
                        @endif

                        <span class="rounded bg-slate-800/90 border border-slate-700 px-2 py-1 text-xs font-bold text-slate-300">
                            {{ $movie->age_rating }}
                        </span>
                        <span class="rounded bg-slate-800/90 border border-slate-700 px-2 py-1 text-xs font-bold text-slate-300">
                            {{ $movie->release_year }}
                        </span>
                        <span class="rounded bg-slate-800/90 border border-slate-700 px-2 py-1 text-xs font-bold text-slate-300">
                            {{ $movie->durationFormatted() }}
                        </span>
                        <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2 py-1 text-xs font-bold text-amber-400">
                            Luganda Audio
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight font-display">
                        {{ $movie->title }}
                    </h1>
                    @if ($movie->original_title && $movie->original_title !== $movie->title)
                        <p class="text-sm text-slate-400 font-medium mt-1">Original: {{ $movie->original_title }} ({{ $movie->country_of_origin }})</p>
                    @endif

                    <!-- Ratings Bar -->
                    <div class="mt-4 flex items-center justify-center md:justify-start space-x-4">
                        <div class="flex items-center text-amber-400 font-bold text-base">
                            <x-bi-star-fill class="h-5 w-5 mr-1 text-amber-400" />
                            <span>{{ number_format($movie->average_rating, 1) }}</span>
                            <span class="text-xs text-slate-400 ml-1.5 font-normal">({{ number_format($movie->ratings_count) }} ratings)</span>
                        </div>
                        <span class="text-slate-600">•</span>
                        <div class="text-xs text-slate-400">
                            <span class="font-bold text-white">{{ number_format($movie->views) }}</span> streams
                        </div>
                    </div>

                    <!-- Synopsis / Description -->
                    <p class="mt-4 text-sm sm:text-base text-slate-300 leading-relaxed max-w-3xl">
                        {{ $movie->synopsis ?: $movie->description }}
                    </p>

                    <!-- Genres Pills -->
                    <div class="mt-5 flex flex-wrap items-center justify-center md:justify-start gap-2">
                        @foreach ($movie->genres as $genre)
                            <span class="rounded-lg bg-slate-800/80 border border-slate-700/60 px-3 py-1 text-xs font-medium text-slate-300">
                                {{ $genre->name }}
                            </span>
                        @endforeach
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-wrap items-center justify-center md:justify-start gap-4">
                        <a href="#player" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-3.5 text-sm font-extrabold text-black shadow-xl shadow-amber-500/20 hover:from-amber-400 hover:to-yellow-400 transition-all hover:scale-105">
                            <x-bi-play-fill class="h-6 w-6 mr-1.5" />
                            STREAM IN LUGANDA
                        </a>

                        @if ($movie->trailer_url)
                            <a href="{{ $movie->trailer_url }}" target="_blank" rel="noopener" class="inline-flex items-center justify-center rounded-xl bg-slate-800/90 hover:bg-slate-700 border border-slate-700 px-5 py-3.5 text-sm font-bold text-slate-200 transition-all">
                                <x-bi-film class="h-4 w-4 mr-2 text-amber-400" />
                                Watch Trailer
                            </a>
                        @endif

                        <button type="button" class="inline-flex items-center justify-center rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-800 px-4 py-3.5 text-sm font-semibold text-slate-300 transition-all">
                            <x-bi-plus-circle class="h-5 w-5 mr-2 text-slate-400" />
                            My List
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Stream Video Player Section -->
    <div id="player" class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-video-player 
            :src="route('stream.movie', $movie->slug)" 
            :poster="$movie->backdropUrl()" 
            :title="$movie->title" 
            :vj="$movie->vj ? $movie->vj->stage_name : null" 
            type="movie" 
            :id="$movie->id" />
    </div>

    <!-- Related Sections -->
    <main class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8 space-y-12">
        <!-- More from this VJ -->
        @if ($movie->vj && $moreFromVj->isNotEmpty())
            <section>
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                            <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                            More Translations by {{ $movie->vj->stage_name }}
                        </h2>
                        <p class="text-xs text-slate-400">Top Luganda movies voiced by {{ $movie->vj->stage_name }}</p>
                    </div>
                    <a href="/vjs/{{ $movie->vj->slug }}" class="text-xs font-bold text-amber-400 hover:underline">
                        View All by {{ $movie->vj->stage_name }} &rarr;
                    </a>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
                    @foreach ($moreFromVj as $relMovie)
                        <x-velflix-card :movie="$relMovie" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Similar / Recommended Movies -->
        @if ($similarMovies->isNotEmpty())
            <section>
                <div class="mb-4">
                    <h2 class="text-xl font-extrabold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        Trending in Uganda
                    </h2>
                    <p class="text-xs text-slate-400">Popular movies watched across Kampala and Uganda today</p>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
                    @foreach ($similarMovies as $simMovie)
                        <x-velflix-card :movie="$simMovie" />
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-footer />
</x-layout>
