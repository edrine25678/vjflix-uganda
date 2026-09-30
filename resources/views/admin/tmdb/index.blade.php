<x-admin-layout>
    <x-slot:title>TMDB Resources & Importer — VJFlix CMS</x-slot:title>

    <div class="space-y-6" x-data="{
        importModalOpen: false,
        importItem: { id: '', title: '', type: 'movie', poster: '' },
        openImport(id, title, type, poster) {
            this.importItem = { id, title, type, poster };
            this.importModalOpen = true;
        }
    }">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-extrabold text-white font-display">
                        TMDB Resources & Importer
                    </h1>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-400 border border-amber-500/30 uppercase tracking-wider">
                        v3 API
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Search and import official movie metadata, high-resolution artwork, and synopsis directly from The Movie Database into your VJ catalog.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.movies.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all">
                    <x-bi-film class="h-4 w-4 text-amber-400" />
                    New Movie
                </a>
                <a href="{{ route('admin.series.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all">
                    <x-bi-tv class="h-4 w-4 text-amber-400" />
                    New Series
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold flex items-center gap-2">
                <x-bi-check-circle class="h-4 w-4 flex-shrink-0" />
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-semibold flex items-center gap-2">
                <x-bi-exclamation-triangle class="h-4 w-4 flex-shrink-0" />
                {{ session('error') }}
            </div>
        @endif

        <!-- TMDB Configuration Check Alert -->
        @if (! $configured)
            <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 space-y-2">
                <div class="flex items-center gap-2 font-bold text-sm text-amber-400">
                    <x-bi-key class="h-4 w-4" />
                    TMDB API Token Required
                </div>
                <p class="text-xs text-slate-300">
                    Your <code class="bg-black/40 px-1.5 py-0.5 rounded text-amber-400 font-mono">TMDB_TOKEN</code> is not yet configured in your <code class="bg-black/40 px-1.5 py-0.5 rounded text-amber-400 font-mono">.env</code> file.
                    To unlock live searching, trending feeds, and 1-click importing:
                </p>
                <ol class="list-decimal list-inside text-xs text-slate-300 space-y-1 pl-2">
                    <li>Sign up or log in at <a href="https://www.themoviedb.org/settings/api" target="_blank" class="underline text-amber-400 font-semibold">themoviedb.org/settings/api</a> to generate a free API key or Bearer Read Access Token.</li>
                    <li>Add <code class="bg-black/40 px-1.5 py-0.5 rounded text-amber-400 font-mono">TMDB_TOKEN=your_token_or_api_key_here</code> to your <code class="font-mono">.env</code> file.</li>
                    <li>Refresh this page to instantly browse and import millions of titles.</li>
                </ol>
            </div>
        @endif

        <!-- Search Bar & Filters -->
        <div class="rounded-2xl bg-slate-900 border border-slate-800 p-5 shadow-xl space-y-4">
            <form method="GET" action="{{ route('admin.tmdb.index') }}" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="tab" value="search">

                <div class="relative flex-1">
                    <x-bi-search class="absolute left-3.5 top-3.5 h-4 w-4 text-slate-400" />
                    <input type="text" name="query" value="{{ $query }}" placeholder="Search TMDB for any movie or TV series (e.g. Inception, Breaking Bad, Gladiator)..."
                           class="w-full rounded-xl bg-white border border-slate-300 pl-10 pr-4 py-2.5 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>

                <div class="flex items-center gap-2">
                    <select name="type" class="rounded-xl bg-white border border-slate-300 px-3 py-2.5 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="movie" {{ request('type', 'movie') === 'movie' ? 'selected' : '' }}>Movies</option>
                        <option value="tv" {{ request('type') === 'tv' ? 'selected' : '' }}>TV Series</option>
                    </select>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all flex items-center gap-1.5">
                        <x-bi-search class="h-3.5 w-3.5" />
                        Search
                    </button>
                </div>
            </form>

            <!-- Pre-set Resource Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pt-2 border-t border-slate-800/80">
                <a href="{{ route('admin.tmdb.index', ['tab' => 'popular_movies']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'popular_movies' ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/60 hover:bg-slate-800 text-slate-300' }}">
                    <x-bi-film class="inline-block h-3.5 w-3.5 mr-1" />
                    Popular Movies
                </a>
                <a href="{{ route('admin.tmdb.index', ['tab' => 'trending_movies']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'trending_movies' ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/60 hover:bg-slate-800 text-slate-300' }}">
                    <x-bi-fire class="inline-block h-3.5 w-3.5 mr-1 text-orange-400" />
                    Trending Movies
                </a>
                <a href="{{ route('admin.tmdb.index', ['tab' => 'popular_series']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'popular_series' ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/60 hover:bg-slate-800 text-slate-300' }}">
                    <x-bi-tv class="inline-block h-3.5 w-3.5 mr-1" />
                    Popular TV Series
                </a>
                <a href="{{ route('admin.tmdb.index', ['tab' => 'trending_series']) }}"
                   class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap {{ $tab === 'trending_series' ? 'bg-amber-500 text-black shadow-md shadow-amber-500/20' : 'bg-slate-800/60 hover:bg-slate-800 text-slate-300' }}">
                    <x-bi-lightning-charge class="inline-block h-3.5 w-3.5 mr-1 text-amber-400" />
                    Trending Series
                </a>
            </div>
        </div>

        <!-- TMDB Content Results Grid -->
        @if (count($results) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                @foreach ($results as $item)
                    @php
                        $isTv = isset($item['first_air_date']) || request('type') === 'tv' || str_contains($tab, 'series');
                        $title = $isTv ? ($item['name'] ?? 'Untitled') : ($item['title'] ?? 'Untitled');
                        $date = $isTv ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');
                        $year = $date ? substr($date, 0, 4) : '—';
                        $poster = ! empty($item['poster_path']) ? 'https://image.tmdb.org/t/p/w500' . $item['poster_path'] : 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=500&q=80';
                        $rating = round($item['vote_average'] ?? 0, 1);
                        $type = $isTv ? 'tv' : 'movie';
                    @endphp

                    <div class="group rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden flex flex-col hover:border-amber-500/40 hover:shadow-xl hover:shadow-amber-500/5 transition-all">
                        <!-- Poster Thumbnail -->
                        <div class="relative aspect-[2/3] bg-slate-950 overflow-hidden">
                            <img src="{{ $poster }}" alt="{{ $title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                            <!-- Rating Badge -->
                            <div class="absolute top-2 right-2 flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-black/80 backdrop-blur text-[10px] font-extrabold text-amber-400 border border-amber-500/30">
                                <x-bi-star-fill class="h-2.5 w-2.5" />
                                {{ $rating }}
                            </div>

                            <!-- Media Type Badge -->
                            <div class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-black/80 backdrop-blur text-[9px] font-extrabold text-slate-300 uppercase tracking-wider">
                                {{ $type }}
                            </div>
                        </div>

                        <!-- Card Info -->
                        <div class="p-3.5 flex-1 flex flex-col justify-between space-y-2">
                            <div>
                                <h3 class="text-xs font-bold text-white line-clamp-1 group-hover:text-amber-400 transition-colors" title="{{ $title }}">
                                    {{ $title }}
                                </h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $year }} · ID: <span class="font-mono text-slate-500">{{ $item['id'] }}</span>
                                </p>
                                <p class="text-[10px] text-slate-500 line-clamp-2 mt-1 leading-relaxed">
                                    {{ $item['overview'] ?? 'No overview available from TMDB.' }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="pt-2 border-t border-slate-800/80 flex flex-col gap-1.5">
                                <button type="button"
                                        @click="openImport('{{ $item['id'] }}', '{{ addslashes($title) }}', '{{ $type }}', '{{ $poster }}')"
                                        class="w-full py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-black text-[11px] font-extrabold flex items-center justify-center gap-1 shadow-sm transition-all">
                                    <x-bi-cloud-arrow-down class="h-3.5 w-3.5" />
                                    Import
                                </button>

                                @if ($type === 'movie')
                                    <a href="{{ route('admin.movies.create', ['tmdb_id' => $item['id']]) }}"
                                       class="w-full py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                        <x-bi-pencil-square class="h-3 w-3" />
                                        Auto-fill Create
                                    </a>
                                @else
                                    <a href="{{ route('admin.series.create', ['tmdb_id' => $item['id']]) }}"
                                       class="w-full py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[10px] font-semibold flex items-center justify-center gap-1 transition-colors">
                                        <x-bi-pencil-square class="h-3 w-3" />
                                        Auto-fill Create
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($totalPages > 1)
                <div class="flex items-center justify-center gap-2 pt-4">
                    @if ($page > 1)
                        <a href="{{ route('admin.tmdb.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                           class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                            &larr; Previous
                        </a>
                    @endif

                    <span class="text-xs text-slate-400 font-semibold px-2">
                        Page {{ $page }} of {{ $totalPages }}
                    </span>

                    @if ($page < $totalPages)
                        <a href="{{ route('admin.tmdb.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                           class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold">
                            Next &rarr;
                        </a>
                    @endif
                </div>
            @endif
        @else
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-12 text-center space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-amber-400">
                    <x-bi-film class="h-6 w-6" />
                </div>
                <h3 class="text-sm font-bold text-white">No TMDB Results Found</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    @if (! $configured)
                        Add your <code class="font-mono text-amber-400">TMDB_TOKEN</code> to unlock the TMDB content library.
                    @elseif (! empty($query))
                        No titles matched your search query "<span class="text-white">{{ $query }}</span>". Try searching for a different keyword or movie title.
                    @else
                        Select a category tab above or search by movie title.
                    @endif
                </p>
            </div>
        @endif

        <!-- Quick Import Modal -->
        <div x-show="importModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
            <div @click.away="importModalOpen = false"
                 class="w-full max-w-md rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <h3 class="text-base font-extrabold text-white font-display">
                        Quick Import from TMDB
                    </h3>
                    <button type="button" @click="importModalOpen = false" class="text-slate-400 hover:text-white">
                        <x-bi-x-lg class="h-4 w-4" />
                    </button>
                </div>

                <div class="flex gap-4 items-center">
                    <img :src="importItem.poster" :alt="importItem.title" class="w-16 h-24 object-cover rounded-xl bg-slate-950 flex-shrink-0">
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-white truncate" x-text="importItem.title"></h4>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Type: <span class="uppercase font-bold text-amber-400" x-text="importItem.type"></span> · TMDB ID: <span class="font-mono text-slate-300" x-text="importItem.id"></span>
                        </p>
                        <p class="text-[10px] text-emerald-400 mt-0.5">
                            Auto-fetches overview, backdrop, runtime &amp; genres.
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.tmdb.import') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="tmdb_id" :value="importItem.id">
                    <input type="hidden" name="type" :value="importItem.type">

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Assign Ugandan VJ (Optional)</label>
                        <select name="vj_id" class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="">-- No VJ / Original Translation --</option>
                            @foreach ($vjs as $vj)
                                <option value="{{ $vj->id }}">{{ $vj->stage_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1.5">Initial Publication Status</label>
                        <select name="status" class="w-full rounded-xl bg-white border border-slate-300 px-3 py-2 text-xs text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="draft" selected>Draft (Recommended - allows adding video stream URL)</option>
                            <option value="published">Published immediately</option>
                        </select>
                    </div>

                    <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-800">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20">
                            Confirm &amp; Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
