<x-admin-layout>
    <x-slot:title>Add New Movie — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-4xl mx-auto" x-data="{
        title: '{{ addslashes(old('title', $tmdbPrefill['translated_title_suggestion'] ?? ($tmdbPrefill['title'] ?? ''))) }}',
        originalTitle: '{{ addslashes(old('original_title', $tmdbPrefill['original_title'] ?? '')) }}',
        releaseYear: '{{ old('release_year', $tmdbPrefill['release_year'] ?? date('Y')) }}',
        duration: '{{ old('duration', $tmdbPrefill['duration'] ?? 120) }}',
        synopsis: '{{ addslashes(old('synopsis', $tmdbPrefill['synopsis'] ?? '')) }}',
        description: '{{ addslashes(old('description', $tmdbPrefill['description'] ?? '')) }}',
        videoUrl: '{{ old('video_url', '') }}',
        trailerUrl: '{{ old('trailer_url', $tmdbPrefill['trailer_url'] ?? '') }}',
        posterUrl: '{{ old('poster_url', $tmdbPrefill['poster_url'] ?? '') }}',
        backdropUrl: '{{ old('backdrop_url', $tmdbPrefill['backdrop_url'] ?? '') }}',
        tmdbQuery: '',
        tmdbResults: [],
        loadingTmdb: false,
        showTmdbDropdown: false,
        autoFilledNotice: false,
        selectedMovieTitle: '',

        searchTmdb(explicitQuery = '') {
            const q = explicitQuery.trim() || this.tmdbQuery.trim() || this.title.trim();
            if (q.length < 2) return;
            this.loadingTmdb = true;
            fetch(`/admin/tmdb/search?type=movie&query=${encodeURIComponent(q)}`)
                .then(res => res.json())
                .then(data => {
                    this.tmdbResults = data.results || [];
                    this.showTmdbDropdown = this.tmdbResults.length > 0;
                })
                .catch(() => { this.tmdbResults = []; })
                .finally(() => { this.loadingTmdb = false; });
        },

        selectTmdbMovie(tmdbId) {
            this.loadingTmdb = true;
            fetch(`/admin/tmdb/details/movie/${tmdbId}`)
                .then(res => res.json())
                .then(data => {
                    if (data && !data.error) {
                        this.title = data.translated_title_suggestion || data.title;
                        this.originalTitle = data.original_title || data.title;
                        this.releaseYear = data.release_year || '';
                        this.duration = data.duration || 120;
                        this.synopsis = data.synopsis || '';
                        this.description = data.description || '';
                        this.posterUrl = data.poster_url || '';
                        this.backdropUrl = data.backdrop_url || '';
                        this.trailerUrl = data.trailer_url || '';
                        this.selectedMovieTitle = data.title;

                        // Check genre checkboxes
                        if (data.genre_ids && Array.isArray(data.genre_ids)) {
                            document.querySelectorAll('input[name=\'genres[]\']').forEach(cb => {
                                cb.checked = data.genre_ids.includes(parseInt(cb.value));
                            });
                        }

                        this.autoFilledNotice = true;
                        this.showTmdbDropdown = false;
                        this.tmdbResults = [];
                    }
                })
                .catch(err => console.error(err))
                .finally(() => { this.loadingTmdb = false; });
        }
    }">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.movies.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Movies
                </a>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Add Movie Translation
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">
                    Search any movie to auto-fill title, artwork, runtime, synopsis &amp; genres from TMDB.
                </p>
            </div>
            <a href="{{ route('admin.tmdb.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-bold border border-slate-700 transition-colors">
                <x-bi-cloud-arrow-down class="h-3.5 w-3.5" />
                Browse TMDB Feeds
            </a>
        </div>

        <!-- TMDB Live Auto-fill Bar (Directly at point of adding movie) -->
        <div class="rounded-2xl bg-gradient-to-r from-slate-900 via-slate-900 to-amber-950/40 border border-amber-500/40 p-5 shadow-xl space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-xs">
                        <x-bi-lightning-charge-fill class="h-4 w-4" />
                    </span>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-amber-400">
                        TMDB Auto-Fill Search
                    </h2>
                </div>
                <span class="text-[11px] text-slate-400 hidden sm:inline">
                    Auto-fills all fields below in 1 click
                </span>
            </div>

            <div class="relative">
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <x-bi-search class="absolute left-3.5 top-3.5 h-3.5 w-3.5 text-slate-400" />
                        <input type="text"
                               x-model="tmdbQuery"
                               @keydown.enter.prevent="searchTmdb()"
                               @input.debounce.350ms="searchTmdb()"
                               @keydown.escape="showTmdbDropdown = false"
                               placeholder="Type movie name to auto-fill (e.g. Avengers Endgame, Inception, Gladiator II, 299534)..."
                               class="w-full rounded-xl bg-white border border-slate-300 pl-9 pr-8 py-2.5 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">

                        <div x-show="loadingTmdb" class="absolute right-3 top-3">
                            <svg class="animate-spin h-4 w-4 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                        </div>
                    </div>

                    <button type="button"
                            @click="searchTmdb()"
                            class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all flex items-center gap-1.5 flex-shrink-0">
                        <x-bi-search class="h-3.5 w-3.5" />
                        Search TMDB
                    </button>
                </div>

                <!-- Dropdown suggestions -->
                <div x-show="showTmdbDropdown && tmdbResults.length > 0"
                     @click.away="showTmdbDropdown = false"
                     x-cloak
                     class="absolute left-0 right-0 z-50 mt-1 max-h-80 overflow-y-auto rounded-xl bg-slate-900 border border-amber-500/40 shadow-2xl divide-y divide-slate-800">
                    <template x-for="item in tmdbResults" :key="item.id">
                        <div @click="selectTmdbMovie(item.id)"
                             class="flex items-center gap-3 p-3 hover:bg-slate-800 cursor-pointer transition-colors group">
                            <img :src="item.poster || 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?auto=format&fit=crop&w=100&q=80'"
                                 :alt="item.title"
                                 class="w-10 h-14 object-cover rounded-lg bg-slate-950 flex-shrink-0">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold text-white group-hover:text-amber-400 truncate" x-text="item.title"></h4>
                                    <span class="text-[10px] text-slate-400 font-mono" x-text="item.year"></span>
                                    <span class="text-[10px] text-amber-400 font-bold" x-text="'★ ' + item.rating"></span>
                                </div>
                                <p class="text-[10px] text-slate-400 line-clamp-1 mt-0.5" x-text="item.overview"></p>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="px-2.5 py-1.5 rounded-lg bg-amber-500 text-black text-[10px] font-extrabold group-hover:bg-amber-400 transition-colors">
                                    Auto-fill Form &rarr;
                                </span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Auto-fill Success Alert -->
            <div x-show="autoFilledNotice" x-cloak class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-bi-check-circle-fill class="h-4 w-4 text-emerald-400 flex-shrink-0" />
                    <span>Auto-filled form with <span class="text-white font-bold" x-text="selectedMovieTitle"></span> from TMDB! Artwork, overview, and genres populated.</span>
                </div>
                <button type="button" @click="autoFilledNotice = false" class="text-emerald-400 hover:text-white">✕</button>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf

            <!-- Title & Release Year -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">Movie Title *</label>
                        <button type="button" @click="searchTmdb(title)" class="text-[11px] text-amber-400 hover:underline flex items-center gap-1 font-semibold">
                            <x-bi-lightning-charge class="h-3 w-3" />
                            Auto-fill from this title
                        </button>
                    </div>
                    <input type="text"
                           name="title"
                           x-model="title"
                           required
                           placeholder="e.g. John Wick 4 (Luganda)"
                           class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Release Year</label>
                    <input type="number"
                           name="release_year"
                           x-model="releaseYear"
                           class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Original Title & Trailer URL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Original English/Foreign Title</label>
                    <input type="text"
                           name="original_title"
                           x-model="originalTitle"
                           placeholder="e.g. John Wick: Chapter 4"
                           class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Trailer URL (YouTube/MP4)</label>
                    <input type="url"
                           name="trailer_url"
                           x-model="trailerUrl"
                           placeholder="https://www.youtube.com/watch?v=..."
                           class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- VJ & Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ugandan Video Jockey (VJ)</label>
                    <select name="vj_id" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">-- Select VJ --</option>
                        @foreach ($vjs as $vj)
                            <option value="{{ $vj->id }}" {{ old('vj_id') == $vj->id ? 'selected' : '' }}>
                                {{ $vj->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Duration (minutes)</label>
                    <input type="number"
                           name="duration"
                           x-model="duration"
                           class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Synopsis & Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Short Synopsis</label>
                <textarea name="synopsis"
                          x-model="synopsis"
                          rows="2"
                          placeholder="Brief summary of the movie..."
                          class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Description</label>
                <textarea name="description"
                          x-model="description"
                          rows="4"
                          placeholder="Detailed plot and VJ translation notes..."
                          class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"></textarea>
            </div>

            <!-- Video Media Source: Upload from Device or Paste URL -->
            <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-400">
                            Movie Video Source *
                        </label>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Upload a video directly from your device (MP4, WebM, MKV) or paste a video streaming URL.
                        </p>
                    </div>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700">
                        Player Source
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                    <!-- Option 1: Upload from Local Device -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-amber-500/20 text-amber-400 text-xs">
                                <x-bi-upload class="h-3.5 w-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white">Option 1: Upload from Computer</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Select an MP4, WebM, or MKV file from your local storage.</p>
                        <input type="file"
                               name="video_file"
                               accept="video/mp4,video/webm,video/quicktime,video/x-matroska,.mp4,.webm,.mov,.mkv"
                               class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
                    </div>

                    <!-- Option 2: Paste Stream URL -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-colors space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400 text-xs">
                                <x-bi-link-45deg class="h-3.5 w-3.5" />
                            </span>
                            <span class="text-xs font-bold text-white">Option 2: Cloud / CDN Stream URL</span>
                        </div>
                        <p class="text-[10px] text-slate-400">Paste direct .mp4, Cloudflare R2, BunnyCDN, or .m3u8 link.</p>
                        <input type="url"
                               name="video_url"
                               x-model="videoUrl"
                               placeholder="https://.../movie.mp4 or HLS m3u8"
                               class="w-full rounded-xl bg-white border border-slate-300 p-2.5 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Poster Artwork & Backdrop with Instant Live Previews -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Poster Artwork</label>
                    <input type="file" name="poster_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    <p class="text-[10px] text-slate-500">Or paste image URL (auto-populated by TMDB):</p>
                    <input type="url"
                           name="poster_url"
                           x-model="posterUrl"
                           placeholder="https://image.tmdb.org/t/p/w500/..."
                           class="w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">

                    <template x-if="posterUrl">
                        <div class="mt-2 flex items-center gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                            <img :src="posterUrl" alt="Poster Preview" class="w-12 h-16 object-cover rounded bg-black">
                            <span class="text-[10px] text-emerald-400 font-semibold">✓ Poster loaded from TMDB</span>
                        </div>
                    </template>
                </div>

                <div class="space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Backdrop Banner</label>
                    <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    <p class="text-[10px] text-slate-500">Or paste banner URL (auto-populated by TMDB):</p>
                    <input type="url"
                           name="backdrop_url"
                           x-model="backdropUrl"
                           placeholder="https://image.tmdb.org/t/p/original/..."
                           class="w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">

                    <template x-if="backdropUrl">
                        <div class="mt-2 flex items-center gap-3 p-2 rounded-lg bg-slate-900 border border-slate-800">
                            <img :src="backdropUrl" alt="Backdrop Preview" class="w-24 h-14 object-cover rounded bg-black">
                            <span class="text-[10px] text-emerald-400 font-semibold">✓ Banner loaded from TMDB</span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Genres Multi-Select -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Genres</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach ($genres as $genre)
                        <label class="flex items-center space-x-2 rounded-lg bg-slate-950 border border-slate-800 p-2.5 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500" {{ in_array($genre->id, old('genres', $tmdbPrefill['genre_ids'] ?? [])) ? 'checked' : '' }}>
                            <span class="text-xs text-slate-300">{{ $genre->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Status, Featured, Trending -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Publication Status</label>
                    <select name="status" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured') ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="featured" class="text-xs font-bold text-slate-300">Feature on Hero Banner</label>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="trending" id="trending" value="1" {{ old('trending') ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="trending" class="text-xs font-bold text-slate-300">Mark as Trending</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.movies.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-xs font-bold text-slate-300 hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                    Save Movie
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
