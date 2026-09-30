<x-layout>
    <x-slot:title>VJFlix Uganda - Stream Translated Movies & Series</x-slot:title>

    <x-header />

<div class="min-h-screen bg-black">
    <!-- Dynamic Auto-Rotating Hero Section (3 Seconds) -->
    @php
        $featuredMovies = \App\Models\Movie::where('status', 'published')
            ->where('featured', true)
            ->with(['vj', 'genres'])
            ->orderBy('views', 'desc')
            ->limit(5)
            ->get();

        if ($featuredMovies->isEmpty()) {
            $featuredMovies = \App\Models\Movie::where('status', 'published')
                ->with(['vj', 'genres'])
                ->orderBy('views', 'desc')
                ->limit(5)
                ->get();
        }
    @endphp

    @if($featuredMovies->isNotEmpty())
        <div x-data="{
                active: 0,
                total: {{ $featuredMovies->count() }},
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
            class="relative h-[70vh] min-h-[500px] bg-gradient-to-b from-transparent to-black overflow-hidden flex items-center">
            
            @foreach($featuredMovies as $fIdx => $fMovie)
                <!-- Backdrop Slide -->
                <div x-show="active === {{ $fIdx }}"
                     x-transition:enter="transition ease-out duration-700"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-500"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute inset-0">
                    <img src="{{ $fMovie->backdrop ?? $fMovie->poster }}" 
                         alt="{{ $fMovie->title }}" 
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent"></div>
                </div>

                <!-- Slide Content -->
                <div x-show="active === {{ $fIdx }}"
                     x-transition:enter="transition ease-out duration-500 delay-100"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-4"
                     class="relative z-10 h-full flex items-center px-4 sm:px-8 md:px-16 w-full">
                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-2 sm:mb-4">
                            <span class="rounded bg-slate-900/90 border border-slate-700 px-2 py-0.5 text-xs text-gray-300 font-bold">{{ $fMovie->release_year }}</span>
                            @if($fMovie->vj)
                                <span class="rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-400 px-3 py-0.5 text-xs font-bold">
                                    Voiced by {{ $fMovie->vj->stage_name }}
                                </span>
                            @endif
                            @if($fMovie->average_rating)
                                <span class="flex items-center gap-1 text-yellow-400 text-xs font-bold bg-black/60 px-2 py-0.5 rounded border border-yellow-500/30">
                                    ★ {{ number_format($fMovie->average_rating, 1) }}
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-5xl md:text-6xl font-bold text-white mb-2 sm:mb-4 tracking-tight leading-none font-display">
                            {{ $fMovie->title }}
                        </h1>

                        <p class="text-gray-300 text-xs sm:text-base mb-4 sm:mb-6 line-clamp-2 sm:line-clamp-3 leading-relaxed">
                            {{ $fMovie->synopsis ?? $fMovie->description }}
                        </p>

                        <div class="flex flex-wrap gap-2.5 sm:gap-4">
                            <a href="{{ route('movies.show', $fMovie->slug) }}" 
                               class="bg-amber-500 text-black px-5 sm:px-7 py-2.5 sm:py-3 rounded-xl font-extrabold hover:bg-amber-400 transition flex items-center gap-2 text-xs sm:text-sm shadow-xl shadow-amber-500/20 hover:scale-105">
                                <x-bi-play-fill class="w-5 h-5" />
                                Play Now
                            </a>
                            <a href="{{ route('movies.download', $fMovie->slug) }}" 
                               class="bg-slate-900/90 border border-slate-700 hover:border-amber-500 hover:bg-amber-500 hover:text-black text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-bold transition flex items-center gap-2 text-xs sm:text-sm shadow-lg group">
                                <x-bi-download class="w-4 h-4 text-amber-400 group-hover:text-black transition-colors" />
                                Download
                            </a>
                            <a href="{{ route('movies.show', $fMovie->slug) }}" 
                               class="bg-slate-800/80 text-white px-4 sm:px-6 py-2.5 sm:py-3 rounded-xl font-semibold hover:bg-slate-700 transition text-xs sm:text-sm border border-slate-700">
                                More Info
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Auto-slider Indicators / Dots -->
            @if($featuredMovies->count() > 1)
                <div class="absolute bottom-6 right-4 sm:right-8 md:right-16 z-30 flex items-center space-x-2">
                    @foreach($featuredMovies as $dotIdx => $dotMovie)
                        <button @click="selectSlide({{ $dotIdx }})" 
                                type="button"
                                class="h-1.5 sm:h-2 rounded-full transition-all duration-300" 
                                :class="active === {{ $dotIdx }} ? 'w-6 sm:w-8 bg-amber-500 shadow-md shadow-amber-500/50' : 'w-2 bg-white/40 hover:bg-white/80'"
                                title="Slide {{ $dotIdx + 1 }}: {{ $dotMovie->title }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Content Sections -->
    <div class="px-3 sm:px-8 md:px-16 py-8 -mt-20 relative z-20">
        @if(auth()->check())
            <x-recommendations type="personalized" title="Recommended For You" :limit="10" />
        @endif

        @php
            $trendingMovies = \App\Models\Movie::where('status', 'published')
                ->where('trending', true)
                ->with(['vj', 'genres'])
                ->orderBy('views', 'desc')
                ->limit(10)
                ->get();
        @endphp
        <x-movies :movies="$trendingMovies" category="Trending Now" />

        @php
            $newReleases = \App\Models\Movie::where('status', 'published')
                ->with(['vj', 'genres'])
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();
        @endphp
        <x-movies :movies="$newReleases" category="New Releases" />

        @php
            $featuredVjs = \App\Models\Vj::where('is_active', true)
                ->withCount(['movies', 'series'])
                ->orderBy('movies_count', 'desc')
                ->limit(5)
                ->get();
        @endphp

        @if($featuredVjs->count() > 0)
            <section class="my-8">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        Featured VJs
                    </h2>
                </div>
                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth gap-2 sm:gap-4">
                    @foreach($featuredVjs as $vj)
                        <a href="{{ route('vjs.show', $vj->slug) }}" class="flex-shrink-0 w-24 sm:w-48 group cursor-pointer">
                            <div class="relative rounded-lg overflow-hidden bg-gray-800 aspect-[3/4]">
                                <img src="{{ $vj->cover_photo ?? asset('img/default-cover.jpg') }}" 
                                     alt="{{ $vj->stage_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-2 sm:p-4">
                                    <div class="text-white font-semibold text-xs sm:text-base truncate">{{ $vj->stage_name }}</div>
                                    <div class="text-gray-300 text-[10px] sm:text-sm">{{ $vj->movies_count }} Movies</div>
                                </div>
                                @if($vj->is_verified)
                                    <div class="absolute top-3 right-3 bg-blue-500 rounded-full p-1">
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                    <a href="{{ route('vjs.index') }}" class="flex-shrink-0 w-24 sm:w-48 group cursor-pointer flex flex-col items-center justify-center rounded-lg bg-gradient-to-b from-slate-900 to-slate-950 border border-slate-800 hover:border-amber-500/80 p-3 aspect-[3/4] text-center shadow-lg transition-all hover:-translate-y-1">
                        <div class="h-8 w-8 sm:h-12 sm:w-12 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:bg-amber-500 group-hover:text-black mb-2 transition-all">
                            <x-bi-arrow-right class="h-4 w-4 sm:h-5 sm:w-5" />
                        </div>
                        <span class="text-white font-bold text-[11px] sm:text-sm group-hover:text-amber-400">See More</span>
                        <span class="text-slate-400 text-[9px] sm:text-xs mt-0.5">All VJs &rarr;</span>
                    </a>
                </div>
            </section>
        @endif

        @php
            $genres = \App\Models\Genre::where('is_active', true)
                ->has('movies')
                ->withCount('movies')
                ->orderBy('movies_count', 'desc')
                ->limit(8)
                ->get();
        @endphp

        @if($genres->count() > 0)
            <section class="my-8">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-purple-500 inline-block"></span>
                        Browse by Genre
                    </h2>
                </div>
                <div class="grid grid-cols-4 md:grid-cols-4 lg:grid-cols-8 gap-2 sm:gap-4">
                    @foreach($genres as $genre)
                        <a href="{{ route('vjflix.index', ['genre' => $genre->slug]) }}"
                           class="bg-gray-800 hover:bg-gray-700 rounded-lg p-2 sm:p-4 text-center transition group">
                            <div class="text-white font-semibold text-xs sm:text-sm truncate">{{ $genre->name }}</div>
                            <div class="text-gray-400 text-[10px] sm:text-xs mt-0.5 sm:mt-1">{{ $genre->movies_count }} Movies</div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <!-- Newsletter Section -->
    <section class="py-20 px-8 md:px-16">
        <x-newsletter />
    </section>
</div>
</x-layout>

