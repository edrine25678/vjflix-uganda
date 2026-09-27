<x-layout>
    <x-slot:title>My List & Bookmarks — VJFlix Uganda</x-slot:title>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ tab: 'all' }">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8 pb-6 border-b border-neutral-800">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs font-semibold mb-3">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z" />
                    </svg>
                    <span>Personal Library</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight">My Saved Titles</h1>
                <p class="text-neutral-400 text-sm mt-1">
                    Your customized queue of Ugandan VJ translated movies and TV series.
                </p>
            </div>

            {{-- Tabs --}}
            <div class="flex items-center gap-2 bg-neutral-900 border border-neutral-800 p-1 rounded-xl">
                <button 
                    @click="tab = 'all'" 
                    :class="tab === 'all' ? 'bg-amber-500 text-neutral-950 font-bold' : 'text-neutral-400 hover:text-white font-medium'"
                    class="px-4 py-1.5 rounded-lg text-xs transition duration-150"
                >
                    All ({{ $watchlists->count() }})
                </button>
                <button 
                    @click="tab = 'movies'" 
                    :class="tab === 'movies' ? 'bg-amber-500 text-neutral-950 font-bold' : 'text-neutral-400 hover:text-white font-medium'"
                    class="px-4 py-1.5 rounded-lg text-xs transition duration-150"
                >
                    Movies ({{ $movies->count() }})
                </button>
                <button 
                    @click="tab = 'series'" 
                    :class="tab === 'series' ? 'bg-amber-500 text-neutral-950 font-bold' : 'text-neutral-400 hover:text-white font-medium'"
                    class="px-4 py-1.5 rounded-lg text-xs transition duration-150"
                >
                    TV Series ({{ $series->count() }})
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm flex items-center justify-between">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($watchlists->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-20 bg-neutral-900/40 border border-dashed border-neutral-800 rounded-3xl p-8 max-w-xl mx-auto">
                <div class="w-16 h-16 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    🔖
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Your List is Empty</h3>
                <p class="text-neutral-400 text-sm mb-6 leading-relaxed">
                    Explore translated blockbusters and series voiced by top Ugandan VJs, then click <strong>Add to List</strong> to save them here for later.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('velflix.index') }}" class="px-5 py-2.5 rounded-xl bg-amber-500 text-neutral-950 font-semibold text-sm hover:bg-amber-400 transition">
                        Browse Movies
                    </a>
                    <a href="{{ route('series.index') }}" class="px-5 py-2.5 rounded-xl bg-neutral-800 text-white font-semibold text-sm hover:bg-neutral-700 transition">
                        Browse TV Series
                    </a>
                </div>
            </div>
        @else
            {{-- Items Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                @foreach($watchlists as $item)
                    @php
                        $media = $item->watchable;
                        if (! $media) continue;
                        $isMovie = $media instanceof \App\Models\Movie;
                    @endphp
                    <div 
                        x-show="tab === 'all' || (tab === 'movies' && {{ $isMovie ? 'true' : 'false' }}) || (tab === 'series' && {{ ! $isMovie ? 'true' : 'false' }})"
                        class="group relative bg-neutral-900 border border-neutral-800/80 rounded-2xl overflow-hidden hover:border-amber-500/50 hover:shadow-xl hover:shadow-amber-500/10 transition-all duration-300 flex flex-col justify-between"
                    >
                        {{-- Poster Image --}}
                        <div class="aspect-[2/3] w-full overflow-hidden bg-neutral-950 relative">
                            <img 
                                src="{{ $media->posterUrl() }}" 
                                alt="{{ $media->title }}" 
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                loading="lazy"
                            >
                            
                            {{-- VJ Badge --}}
                            @if($media->vj)
                                <div class="absolute top-2 left-2 px-2.5 py-1 rounded-md bg-neutral-950/80 backdrop-blur-md text-[11px] font-bold text-amber-400 border border-amber-500/30">
                                    {{ $media->vj->stage_name }}
                                </div>
                            @endif

                            {{-- Media Type Badge --}}
                            <div class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $isMovie ? 'bg-blue-600/80 text-blue-100' : 'bg-purple-600/80 text-purple-100' }}">
                                {{ $isMovie ? 'Movie' : 'Series' }}
                            </div>

                            {{-- Remove Overlay Button --}}
                            <form action="{{ route('watchlist.toggle') }}" method="POST" class="absolute bottom-2 right-2">
                                @csrf
                                <input type="hidden" name="watchable_type" value="{{ $isMovie ? 'movie' : 'series' }}">
                                <input type="hidden" name="watchable_id" value="{{ $media->id }}">
                                <button type="submit" class="p-2 rounded-lg bg-neutral-950/80 backdrop-blur text-neutral-400 hover:text-red-400 hover:bg-neutral-900 border border-neutral-700/80 transition" title="Remove from list">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        {{-- Metadata --}}
                        <div class="p-3.5 flex flex-col justify-between flex-1">
                            <div>
                                <h3 class="font-bold text-sm text-white line-clamp-1 group-hover:text-amber-400 transition">
                                    {{ $media->title }}
                                </h3>
                                <div class="flex items-center gap-2 mt-1 text-xs text-neutral-400">
                                    <span>{{ $media->release_year ?? $media->first_air_year ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <x-rating-stars :rating="$media->average_rating ?? 5.0" size="sm" :showScore="true" />
                                </div>
                            </div>

                            <a 
                                href="{{ $isMovie ? route('movies.show', $media->slug ?? $media->id) : route('series.show', $media->slug) }}" 
                                class="mt-3 block text-center py-2 rounded-xl text-xs font-semibold bg-neutral-800 hover:bg-amber-500 hover:text-neutral-950 text-neutral-200 transition duration-150"
                            >
                                Watch Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
