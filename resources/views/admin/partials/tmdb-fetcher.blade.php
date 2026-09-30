@props(['type' => 'movie'])

<div x-data="{
    searchQuery: '',
    loading: false,
    results: [],
    showResults: false,
    selectedTitle: '',
    fetchedData: null,
    initialId: '{{ request('tmdb_id', '') }}',

    init() {
        if (this.initialId) {
            this.fetchDetails(this.initialId);
        }
    },

    search() {
        const q = this.searchQuery.trim();
        if (q.length < 2) {
            this.results = [];
            this.showResults = false;
            return;
        }

        this.loading = true;
        fetch(`/admin/tmdb/search?type={{ $type }}&query=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                this.results = data.results || [];
                this.showResults = this.results.length > 0;
            })
            .catch(() => {
                this.results = [];
            })
            .finally(() => {
                this.loading = false;
            });
    },

    fetchDetails(id) {
        this.loading = true;
        fetch(`/admin/tmdb/details/{{ $type }}/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data && !data.error) {
                    this.applyData(data);
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.loading = false;
                this.showResults = false;
            });
    },

    applyData(data) {
        this.fetchedData = data;
        this.selectedTitle = data.title;

        // Auto-fill Title
        const titleInput = document.querySelector('input[name=\'title\']');
        if (titleInput) {
            titleInput.value = data.translated_title_suggestion || data.title;
            titleInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Release Year
        const yearInput = document.querySelector('input[name=\'release_year\'], input[name=\'first_air_year\']');
        if (yearInput && (data.release_year || data.first_air_year)) {
            yearInput.value = data.release_year || data.first_air_year;
            yearInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Duration
        const durationInput = document.querySelector('input[name=\'duration\']');
        if (durationInput && data.duration) {
            durationInput.value = data.duration;
            durationInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Synopsis
        const synopsisInput = document.querySelector('textarea[name=\'synopsis\']');
        if (synopsisInput && data.synopsis) {
            synopsisInput.value = data.synopsis;
            synopsisInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Description
        const descInput = document.querySelector('textarea[name=\'description\']');
        if (descInput && data.description) {
            descInput.value = data.description;
            descInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Poster URL
        const posterInput = document.querySelector('input[name=\'poster_url\']');
        if (posterInput && data.poster_url) {
            posterInput.value = data.poster_url;
            posterInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Backdrop URL
        const backdropInput = document.querySelector('input[name=\'backdrop_url\']');
        if (backdropInput && data.backdrop_url) {
            backdropInput.value = data.backdrop_url;
            backdropInput.dispatchEvent(new Event('input'));
        }

        // Auto-fill Trailer URL
        const trailerInput = document.querySelector('input[name=\'trailer_url\']');
        if (trailerInput && data.trailer_url) {
            trailerInput.value = data.trailer_url;
            trailerInput.dispatchEvent(new Event('input'));
        }

        // Auto-select Genres
        if (data.genre_ids && Array.isArray(data.genre_ids)) {
            document.querySelectorAll('input[name=\'genres[]\']').forEach(cb => {
                if (data.genre_ids.includes(parseInt(cb.value))) {
                    cb.checked = true;
                }
            });
        }
    }
}" class="rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-amber-950/30 border border-amber-500/30 p-5 shadow-xl space-y-4">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-sm">
                <x-bi-lightning-charge-fill class="h-4 w-4" />
            </span>
            <div>
                <h3 class="text-sm font-extrabold text-white font-display tracking-wide">
                    TMDB Quick Auto-fill
                </h3>
                <p class="text-[11px] text-slate-400">
                    Type a title or TMDB ID to instantly fetch artwork, overview, duration &amp; genres.
                </p>
            </div>
        </div>

        <a href="{{ route('admin.tmdb.index') }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-400 hover:text-amber-300">
            Open TMDB Explorer
            <x-bi-arrow-up-right class="h-3 w-3" />
        </a>
    </div>

    <!-- Search Input & Live Dropdown -->
    <div class="relative">
        <div class="flex gap-2">
            <div class="relative flex-1">
                <x-bi-search class="absolute left-3.5 top-3.5 h-3.5 w-3.5 text-slate-400" />
                <input type="text"
                       x-model="searchQuery"
                       @input.debounce.300ms="search()"
                       @keydown.escape="showResults = false"
                       placeholder="Search {{ $type === 'tv' ? 'TV series' : 'movie' }} on TMDB (e.g. Oppenheimer, 872585, Dune)..."
                       class="w-full rounded-xl bg-white border border-slate-300 pl-9 pr-8 py-2.5 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">

                <div x-show="loading" class="absolute right-3 top-3">
                    <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>

            <button type="button" @click="search()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold transition-colors">
                Fetch
            </button>
        </div>

        <!-- Autocomplete Suggestions Dropdown -->
        <div x-show="showResults && results.length > 0"
             @click.away="showResults = false"
             x-cloak
             class="absolute left-0 right-0 z-50 mt-1 max-h-80 overflow-y-auto rounded-xl bg-slate-900 border border-slate-700 shadow-2xl divide-y divide-slate-800">
            <template x-for="item in results" :key="item.id">
                <div @click="fetchDetails(item.id)"
                     class="flex items-center gap-3 p-3 hover:bg-slate-800/80 cursor-pointer transition-colors group">
                    <img :src="item.poster || 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=100&q=80'"
                         :alt="item.title"
                         class="w-10 h-14 object-cover rounded-lg bg-slate-950 flex-shrink-0">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-bold text-white group-hover:text-amber-400 truncate" x-text="item.title"></h4>
                            <span class="text-[10px] text-slate-400 font-mono" x-text="item.year"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" x-text="item.overview"></p>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-400 text-[10px] font-extrabold group-hover:bg-amber-500 group-hover:text-black transition-colors">
                            Auto-fill &rarr;
                        </span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Active Fetched Resource Preview Banner -->
    <div x-show="fetchedData" x-cloak class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <template x-if="fetchedData && fetchedData.poster_url">
                <img :src="fetchedData.poster_url" alt="Artwork" class="w-10 h-14 object-cover rounded-lg flex-shrink-0">
            </template>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-white truncate" x-text="selectedTitle"></span>
                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-400 font-bold">TMDB Synced</span>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">
                    Year: <span class="text-slate-200" x-text="fetchedData?.release_year || fetchedData?.first_air_year"></span> ·
                    Duration: <span class="text-slate-200" x-text="(fetchedData?.duration || '—') + ' mins'"></span> ·
                    Genres: <span class="text-slate-200" x-text="(fetchedData?.genre_ids?.length || 0) + ' matched'"></span>
                </p>
            </div>
        </div>

        <button type="button" @click="fetchedData = null" class="text-[10px] text-slate-400 hover:text-red-400 transition-colors">
            Dismiss
        </button>
    </div>
</div>
