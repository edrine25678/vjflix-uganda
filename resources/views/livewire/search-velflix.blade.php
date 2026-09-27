<div class="relative" x-data="{ isOpen: true }" @click.away="isOpen = false">
    <div class="relative flex items-center">
        <x-bi-search class="absolute left-3 h-4 w-4 text-slate-400 pointer-events-none" />
        <input
            wire:model.debounce.300ms="searchVelflix"
            @focus="isOpen = true"
            @keydown.escape.window="isOpen = false"
            @keydown.shift.tab="isOpen = false"
            type="text"
            placeholder="Search movies, VJs, genres..."
            class="w-48 sm:w-64 lg:w-80 rounded-full bg-slate-900 border border-slate-700/80 py-1.5 pl-9 pr-4 text-xs text-white placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500 transition-all shadow-inner">

        <div wire:loading class="absolute right-3">
            <svg class="animate-spin h-3.5 w-3.5 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
        </div>
    </div>

    @if (strlen(trim($searchVelflix)) >= 2)
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute right-0 mt-2 w-80 sm:w-96 rounded-xl bg-slate-900/95 backdrop-blur-xl border border-slate-800 p-2 text-xs shadow-2xl z-50 max-h-[80vh] overflow-y-auto">

            {{-- VJs Match Section --}}
            @if ($localVjs->isNotEmpty())
                <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-400">
                    Video Jockeys
                </div>
                <div class="mb-2 space-y-1">
                    @foreach ($localVjs as $vj)
                        <a href="/vjs/{{ $vj->slug }}" class="flex items-center space-x-3 rounded-lg p-2 hover:bg-slate-800 transition-colors">
                            <img src="{{ $vj->avatarUrl() }}" alt="{{ $vj->stage_name }}" class="h-8 w-8 rounded-full object-cover ring-1 ring-amber-500/50">
                            <div class="flex-grow">
                                <div class="font-bold text-slate-100 flex items-center gap-1">
                                    {{ $vj->stage_name }}
                                    @if ($vj->is_verified)
                                        <x-bi-patch-check-fill class="h-3 w-3 text-amber-400" />
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">{{ $vj->specialization }}</div>
                            </div>
                            <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded">View VJ</span>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Movies Match Section --}}
            @if ($localMovies->isNotEmpty())
                <div class="px-2 py-1 text-[11px] font-bold uppercase tracking-wider text-amber-400">
                    Movies
                </div>
                <div class="space-y-1">
                    @foreach ($localMovies as $movie)
                        <a href="{{ route('movies.show', $movie->slug) }}" class="flex items-center space-x-3 rounded-lg p-2 hover:bg-slate-800 transition-colors">
                            <img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" class="h-12 w-9 rounded object-cover flex-shrink-0 shadow">
                            <div class="flex-grow min-w-0">
                                <div class="font-bold text-slate-100 truncate">{{ $movie->title }}</div>
                                <div class="text-[11px] text-amber-400 flex items-center gap-1">
                                    <span>{{ $movie->vj ? $movie->vj->stage_name : 'Luganda' }}</span>
                                    <span>·</span>
                                    <span>{{ $movie->release_year }}</span>
                                    <span>·</span>
                                    <span>★ {{ number_format($movie->average_rating, 1) }}</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Empty State --}}
            @if ($localMovies->isEmpty() && $localVjs->isEmpty())
                <div class="p-6 text-center text-slate-400">
                    <x-bi-film class="mx-auto h-8 w-8 text-slate-600 mb-2" />
                    <p class="font-semibold text-slate-200">No translations found</p>
                    <p class="text-[11px] mt-1 text-slate-400">No movies or VJs found for "{{ $searchQuery }}". Try searching for <span class="text-amber-400">VJ Junior</span>, <span class="text-amber-400">Action</span>, or <span class="text-amber-400">Avatar</span>.</p>
                </div>
            @endif
        </div>
    @endif
</div>
