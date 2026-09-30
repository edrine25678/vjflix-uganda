<x-layout>
    <x-slot:title>VJFlix Uganda — Your Movies. Your VJs. Your Language.</x-slot:title>

    <x-header />

    <!-- Hero Movie Showcase Banner -->
    <!-- Dynamic Auto-Rotating Hero Banner (Every 3 Seconds) -->
    @php
        $heroList = isset($heroMovies) && $heroMovies->isNotEmpty() ? $heroMovies : (isset($heroMovie) && $heroMovie ? collect([$heroMovie]) : collect());
    @endphp

    @if ($heroList->isNotEmpty())
        <div x-data="{
                active: 0,
                total: {{ $heroList->count() }},
                timer: null,
                startAutoPlay() {
                    this.timer = setInterval(() => {
                        this.active = (this.active + 1) % this.total;
                    }, 3000);
                },
                stopAutoPlay() {
                    if (this.timer) clearInterval(this.timer);
                },
                selectSlide(i) {
                    this.active = i;
                    this.stopAutoPlay();
                    this.startAutoPlay();
                }
            }"
            x-init="startAutoPlay()"
            @mouseenter="stopAutoPlay()"
            @mouseleave="startAutoPlay()"
            class="relative min-h-[500px] sm:min-h-[560px] md:min-h-[620px] w-full overflow-hidden bg-slate-950 flex items-center">
            
            @foreach ($heroList as $hIdx => $hMovie)
                <!-- Backdrop Slide -->
                <div x-show="active === {{ $hIdx }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0">
                    <img src="{{ $hMovie->backdropUrl() }}" alt="{{ $hMovie->title }}" class="h-full w-full object-cover object-center opacity-40">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-950/80 to-transparent"></div>
                </div>

                <!-- Slide Content -->
                <div x-show="active === {{ $hIdx }}"
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 w-full z-10">
                    <div class="max-w-2xl">
                        <!-- VJ Attribution Badge -->
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            @if ($hMovie->vj)
                                <a href="/vjs/{{ $hMovie->vj->slug }}" class="inline-flex items-center gap-1.5 rounded-full bg-amber-500 hover:bg-amber-400 text-black px-3.5 py-1 text-xs font-extrabold uppercase tracking-wide shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                                    <x-bi-mic-fill class="h-3 w-3" />
                                    Translated by {{ $hMovie->vj->stage_name }}
                                </a>
                            @endif
                            <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-slate-300">
                                {{ $hMovie->release_year }}
                            </span>
                            <span class="rounded bg-slate-900/80 backdrop-blur border border-slate-700 px-2 py-0.5 text-xs font-bold text-amber-400">
                                ★ {{ number_format($hMovie->average_rating, 1) }}
                            </span>
                            <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 text-xs font-bold text-amber-400">
                                HD Luganda
                            </span>
                        </div>

                        <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-white tracking-tight leading-none font-display">
                            {{ $hMovie->title }}
                        </h1>

                        <p class="mt-3 sm:mt-4 text-xs sm:text-sm md:text-base text-slate-300 line-clamp-2 sm:line-clamp-3 leading-relaxed">
                            {{ $hMovie->synopsis ?: $hMovie->description }}
                        </p>

                        <!-- CTAs including Download -->
                        <div class="mt-6 sm:mt-8 flex flex-wrap items-center gap-3 sm:gap-4">
                            <a href="{{ route('movies.show', $hMovie->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 px-5 sm:px-6 py-3 sm:py-3.5 text-xs sm:text-sm font-extrabold text-black shadow-xl shadow-amber-500/20 hover:from-amber-400 hover:to-yellow-400 transition-all hover:scale-105">
                                <x-bi-play-fill class="h-5 w-5 sm:h-6 sm:w-6 mr-1" />
                                WATCH NOW
                            </a>

                            <a href="{{ route('movies.download', $hMovie->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900/90 hover:bg-amber-500 hover:text-black border border-slate-700 hover:border-amber-500 px-4 sm:px-5 py-3 sm:py-3.5 text-xs sm:text-sm font-bold text-slate-200 backdrop-blur transition-all shadow-lg group">
                                <x-bi-download class="h-4 w-4 mr-1.5 text-amber-400 group-hover:text-black transition-colors" />
                                Download
                            </a>

                            <a href="{{ route('movies.show', $hMovie->slug) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700 px-4 sm:px-5 py-3 sm:py-3.5 text-xs sm:text-sm font-bold text-slate-300 backdrop-blur transition-all">
                                <x-bi-info-circle class="h-4 w-4 mr-1.5 text-amber-400" />
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Auto-rotating Indicators / Slide Dots -->
            @if ($heroList->count() > 1)
                <div class="absolute bottom-4 sm:bottom-6 right-4 sm:right-8 lg:right-12 z-20 flex items-center space-x-2">
                    @foreach ($heroList as $dotIdx => $dotMovie)
                        <button @click="selectSlide({{ $dotIdx }})" 
                                type="button"
                                class="h-1.5 sm:h-2 rounded-full transition-all duration-300"
                                :class="active === {{ $dotIdx }} ? 'w-6 sm:w-8 bg-amber-500 shadow-md shadow-amber-500/50' : 'w-2 bg-white/30 hover:bg-white/70'"
                                title="Slide {{ $dotIdx + 1 }}: {{ $dotMovie->title }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <main class="mx-auto max-w-7xl px-2.5 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8 sm:space-y-10">
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
                    <a href="/vjs" class="group flex flex-col items-center flex-shrink-0 text-center w-28 sm:w-32">
                        <div class="h-20 w-20 sm:h-24 sm:w-24 rounded-full bg-slate-900 border-2 border-dashed border-amber-500/50 flex flex-col items-center justify-center text-amber-400 group-hover:border-amber-400 group-hover:bg-amber-500 group-hover:text-black transition-all mb-2 shadow-md group-hover:scale-105">
                            <x-bi-arrow-right class="h-6 w-6" />
                        </div>
                        <span class="font-bold text-xs text-white group-hover:text-amber-400 transition-colors">See More</span>
                        <span class="text-[10px] text-slate-400">All VJs &rarr;</span>
                    </a>
                </div>
            </section>
        @endif

        <!-- Continue Watching Shelf -->
        @if (isset($continueWatching) && $continueWatching->isNotEmpty())
            <section class="my-6">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        Continue Watching For {{ auth()->user()->name }} 🍿
                    </h2>
                </div>

                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth space-x-4">
                    @foreach ($continueWatching as $item)
                        @php
                            $target = $item->watchable;
                            $isMovie = $target instanceof \App\Models\Movie;
                            $url = $isMovie 
                                ? route('movies.show', $target->slug) . '#player'
                                : ($target && $target->season && $target->season->series 
                                    ? route('series.watch', [$target->season->series->slug, $target->season->season_number, $target->episode_number])
                                    : '#');
                            $poster = $isMovie ? $target->posterUrl() : ($target ? $target->thumbnailUrl() : '');
                            $title = $isMovie ? $target->title : ($target ? $target->title : 'Episode');
                            $subtitle = $isMovie ? ($target->vj ? $target->vj->stage_name : 'Luganda') : ($target && $target->season && $target->season->series ? $target->season->series->title . ' · S' . $target->season->season_number . ':E' . $target->episode_number : '');
                        @endphp

                        @if ($target)
                            <div class="group relative flex flex-col overflow-hidden rounded-lg sm:rounded-xl bg-slate-900 border border-slate-800 hover:border-amber-500/50 transition-all duration-300 w-44 sm:w-60 flex-shrink-0 shadow-lg mr-2 sm:mr-4">
                                <a href="{{ $url }}" class="relative block aspect-video w-full overflow-hidden bg-slate-800">
                                    <img src="{{ $poster }}" alt="{{ $title }}" class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="rounded-full bg-amber-500 p-2.5 text-black shadow-lg">
                                            <x-bi-play-fill class="h-5 w-5" />
                                        </div>
                                    </div>
                                    <!-- Progress Bar Overlay -->
                                    <div class="absolute bottom-0 inset-x-0 h-1.5 bg-slate-950/80">
                                        <div class="h-full bg-amber-500" style="width: {{ $item->percentage() }}%"></div>
                                    </div>
                                </a>

                                <div class="p-3 flex flex-col justify-between flex-grow">
                                    <div>
                                        <a href="{{ $url }}" class="font-bold text-xs text-white truncate block group-hover:text-amber-400 transition-colors">
                                            {{ $title }}
                                        </a>
                                        <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $subtitle }}</p>
                                    </div>

                                    <div class="mt-2.5 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[10px] text-slate-400">
                                        <span class="text-amber-400 font-bold">{{ $item->percentage() }}% watched</span>
                                        <span>{{ $item->remainingFormatted() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
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
