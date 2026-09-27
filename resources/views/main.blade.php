<x-layout>
    <x-slot:title>VJFlix Uganda — Your Movies. Your VJs. Your Language.</x-slot:title>

    <x-header />

    <!-- Hero Movie Showcase Banner -->
    @if ($heroMovie)
        <div class="relative min-h-[520px] md:min-h-[620px] w-full overflow-hidden bg-slate-950 flex items-center">
            <!-- Backdrop Image -->
            <div class="absolute inset-0">
                <img src="{{ $heroMovie->backdropUrl() }}" alt="{{ $heroMovie->title }}" class="h-full w-full object-cover object-center opacity-40">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 w-full">
                <div class="max-w-2xl">
                    <!-- VJ Attribution Badge -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        @if ($heroMovie->vj)
                            <a href="/vjs/{{ $heroMovie->vj->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 hover:bg-amber-400 text-black px-3.5 py-1 text-xs font-extrabold uppercase tracking-wide shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                                <x-bi-mic-fill class="h-3 w-3" />
                                Translated by {{ $heroMovie->vj->stage_name }}
                            </a>
                        @endif
                        <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-slate-300">
                            {{ $heroMovie->release_year }}
                        </span>
                        <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-amber-400">
                            ★ {{ number_format($heroMovie->average_rating, 1) }}
                        </span>
                        <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-xs font-bold text-amber-400">
                            HD Luganda
                        </span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-extrabold text-white tracking-tight leading-none font-display">
                        {{ $heroMovie->title }}
                    </h1>

                    <p class="mt-4 text-sm sm:text-base text-slate-300 line-clamp-3 leading-relaxed">
                        {{ $heroMovie->synopsis ?: $heroMovie->description }}
                    </p>

                    <!-- CTAs -->
                    <div class="mt-8 flex flex-wrap items-center gap-4">
                        <a href="{{ route('movies.show', $heroMovie->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-6 py-3.5 text-sm font-extrabold text-black shadow-xl shadow-amber-500/20 hover:from-amber-400 hover:to-yellow-400 transition-all hover:scale-105">
                            <x-bi-play-fill class="h-6 w-6 mr-1.5" />
                            WATCH NOW
                        </a>

                        <a href="{{ route('movies.show', $heroMovie->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700 px-5 py-3.5 text-sm font-bold text-slate-200 backdrop-blur transition-all">
                            <x-bi-info-circle class="h-4 w-4 mr-2 text-amber-400" />
                            More Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-10">
        <!-- Popular VJs Showcase Section -->
        @if ($popularVjs->isNotEmpty())
            <section class="my-6">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
                            <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                            Popular Ugandan Video Jockeys
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">Stream movies voiced by your favorite Ugandan narrators</p>
                    </div>
                    <a href="/vjs" class="text-xs font-bold text-amber-400 hover:underline">View All VJs &rarr;</a>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 space-x-5 no-scrollbar scroll-smooth">
                    @foreach ($popularVjs as $vj)
                        <a href="/vjs/{{ $vj->slug }}" class="group flex flex-col items-center flex-shrink-0 text-center w-28 sm:w-32">
                            <div class="relative mb-2">
                                <img src="{{ $vj->avatarUrl() }}" alt="{{ $vj->stage_name }}" class="h-20 w-20 sm:h-24 sm:w-24 rounded-full object-cover ring-2 ring-slate-800 group-hover:ring-amber-500 transition-all duration-300 group-hover:scale-105 shadow-md">
                                @if ($vj->is_verified)
                                    <span class="absolute bottom-0 right-0 rounded-full bg-amber-500 p-1 text-black shadow">
                                        <x-bi-check-lg class="h-3 w-3 stroke-[3]" />
                                    </span>
                                @endif
                            </div>
                            <span class="font-bold text-xs text-slate-200 group-hover:text-amber-400 transition-colors truncate w-full">
                                {{ $vj->stage_name }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $vj->movies_count }} movies</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Trending in Uganda -->
        @if ($trending->isNotEmpty())
            <x-movies :movies="$trending" category="Trending in Uganda 🔥" />
        @endif

        <!-- Latest Translations -->
        @if ($latestTranslations->isNotEmpty())
            <x-movies :movies="$latestTranslations" category="Latest Luganda Translations ✨" />
        @endif

        <!-- Luganda Special -->
        @if ($lugandaSpecial->isNotEmpty())
            <x-movies :movies="$lugandaSpecial" category="Luganda Special (Ekinayuganda) 🇺🇬" />
        @endif

        <!-- Action Movies -->
        @if ($actionMovies->isNotEmpty())
            <x-movies :movies="$actionMovies" category="Action & Tactical Blockbusters 💥" />
        @endif

        <!-- Comedy -->
        @if ($comedyMovies->isNotEmpty())
            <x-movies :movies="$comedyMovies" category="Comedy (Mazina n'Ebisodde) 😂" />
        @endif

        <!-- Sci-Fi -->
        @if ($sciFiMovies->isNotEmpty())
            <x-movies :movies="$sciFiMovies" category="Sci-Fi & Futuristic Fantasy 🚀" />
        @endif
    </main>

    <x-footer />
</x-layout>
