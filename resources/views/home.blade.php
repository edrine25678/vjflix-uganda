<x-layout>
    <x-slot:title>VJFlix Uganda - Stream Translated Movies & Series</x-slot:title>

    <x-header />

<div class="min-h-screen bg-black">
    <!-- Hero Section -->
    @php
        $featuredMovie = \App\Models\Movie::where('status', 'published')
            ->where('featured', true)
            ->with(['vj', 'genres'])
            ->orderBy('views', 'desc')
            ->first();
    @endphp

    @if($featuredMovie)
        <div class="relative h-[70vh] bg-gradient-to-b from-transparent to-black">
            <div class="absolute inset-0">
                <img src="{{ $featuredMovie->backdrop ?? $featuredMovie->poster }}" 
                     alt="{{ $featuredMovie->title }}" 
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-black via-black/30 to-transparent"></div>
            </div>
            
            <div class="relative z-10 h-full flex items-center px-8 md:px-16">
                <div class="max-w-2xl">
                    <h1 class="text-5xl md:text-7xl font-bold text-white mb-4">{{ $featuredMovie->title }}</h1>
                    <div class="flex items-center gap-4 mb-4">
                        <span class="text-gray-300">{{ $featuredMovie->release_year }}</span>
                        @if($featuredMovie->vj)
                            <span class="text-purple-400">Translated by {{ $featuredMovie->vj->stage_name }}</span>
                        @endif
                        @if($featuredMovie->average_rating)
                            <span class="flex items-center gap-1 text-yellow-400">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($featuredMovie->average_rating, 1) }}
                            </span>
                        @endif
                    </div>
                    <p class="text-gray-300 text-lg mb-6 line-clamp-3">{{ $featuredMovie->synopsis ?? $featuredMovie->description }}</p>
                    <div class="flex gap-4">
                        <a href="{{ route('movies.show', $featuredMovie->slug) }}" 
                           class="bg-white text-black px-8 py-3 rounded font-semibold hover:bg-gray-200 transition flex items-center gap-2">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                            </svg>
                            Play Now
                        </a>
                        <a href="{{ route('movies.show', $featuredMovie->slug) }}" 
                           class="bg-gray-600/80 text-white px-8 py-3 rounded font-semibold hover:bg-gray-600 transition">
                            More Info
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Sections -->
    <div class="px-8 md:px-16 py-8 -mt-20 relative z-20">
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
                <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth gap-4">
                    @foreach($featuredVjs as $vj)
                        <a href="{{ route('vjs.show', $vj->slug) }}" class="flex-shrink-0 w-48 group cursor-pointer">
                            <div class="relative rounded-lg overflow-hidden bg-gray-800 aspect-[3/4]">
                                <img src="{{ $vj->cover_photo ?? asset('img/default-cover.jpg') }}" 
                                     alt="{{ $vj->stage_name }}" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="text-white font-semibold">{{ $vj->stage_name }}</div>
                                    <div class="text-gray-300 text-sm">{{ $vj->movies_count }} Movies</div>
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
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                    @foreach($genres as $genre)
                        <a href="{{ route('vjflix.index', ['genre' => $genre->slug]) }}"
                           class="bg-gray-800 hover:bg-gray-700 rounded-lg p-4 text-center transition group">
                            <div class="text-white font-semibold text-sm">{{ $genre->name }}</div>
                            <div class="text-gray-400 text-xs mt-1">{{ $genre->movies_count }} Movies</div>
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

