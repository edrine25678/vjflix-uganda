<x-admin-layout>
    <x-slot:title>Edit Movie: {{ $movie->title }} — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.movies.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Movies
                </a>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Edit: {{ $movie->title }}
                </h1>
            </div>
            <a href="{{ route('movies.show', $movie->slug) }}" target="_blank" class="text-xs font-bold text-amber-400 hover:underline">
                View Live &rarr;
            </a>
        </div>

        <!-- TMDB Quick Fetcher -->
        @include('admin.partials.tmdb-fetcher', ['type' => 'movie'])

        <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data" 
              x-data="movieEditForm()"
              class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf
            @method('PUT')

            <!-- Title & Release Year -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Movie Title *</label>
                    <input type="text" name="title" value="{{ old('title', $movie->title) }}" required class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Release Year</label>
                    <input type="number" name="release_year" value="{{ old('release_year', $movie->release_year) }}" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Original Title & Trailer URL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Original English/Foreign Title</label>
                    <input type="text" name="original_title" value="{{ old('original_title', $movie->original_title) }}" placeholder="e.g. John Wick: Chapter 4" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Trailer URL (YouTube/MP4)</label>
                    <input type="url" name="trailer_url" value="{{ old('trailer_url', $movie->trailer_url) }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- VJ & Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ugandan Video Jockey (VJ)</label>
                    <select name="vj_id" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">-- Select VJ --</option>
                        @foreach ($vjs as $vj)
                            <option value="{{ $vj->id }}" {{ old('vj_id', $movie->vj_id) == $vj->id ? 'selected' : '' }}>
                                {{ $vj->stage_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Duration (minutes)</label>
                    <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Synopsis & Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Short Synopsis</label>
                <textarea name="synopsis" rows="2" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('synopsis', $movie->synopsis) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Description</label>
                <textarea name="description" rows="4" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description', $movie->description) }}</textarea>
            </div>

            <!-- Video Media Source: Upload from Device or Paste URL -->
            <div class="p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-amber-400">
                            Movie Video Source *
                        </label>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Upload a video file from your computer or paste an external streaming link.
                        </p>
                    </div>
                    @if ($movie->video_path)
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            ✓ Local File Uploaded
                        </span>
                    @elseif ($movie->video_url)
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30">
                            ✓ Remote URL Active
                        </span>
                    @endif
                </div>

                @if ($movie->video_path)
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <x-bi-file-earmark-play-fill class="h-4 w-4 text-emerald-400" />
                            <span class="text-slate-300 font-mono text-[11px]">{{ $movie->video_path }}</span>
                        </div>
                        <a href="{{ route('stream.movie', $movie->slug) }}" target="_blank" class="text-amber-400 hover:underline font-bold text-[11px]">
                            Preview Stream &rarr;
                        </a>
                    </div>
                @endif

                <div class="space-y-4 pt-1">
                    <!-- Option 1: Direct Cloudflare R2 Upload Widget -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-amber-500/40 space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-xs">
                                    <x-bi-upload class="h-4 w-4" />
                                </span>
                                <div>
                                    <span class="text-xs font-bold text-white">Direct Upload to Cloudflare R2</span>
                                    <p class="text-[10px] text-slate-400">Streams 2GB - 2.5GB files straight from your browser into your R2 bucket.</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold text-amber-400 px-2 py-0.5 rounded bg-slate-950 border border-slate-800">
                                Recommended
                            </span>
                        </div>

                        <!-- File Input (Disabled while uploading) -->
                        <div x-show="!uploading && !uploadSuccess">
                            <label class="block cursor-pointer">
                                <input type="file"
                                       @change="uploadVideoToR2($event)"
                                       accept="video/mp4,video/webm,video/quicktime,video/x-matroska,.mp4,.webm,.mov,.mkv"
                                       class="w-full text-xs text-slate-400 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-black hover:file:bg-amber-400 cursor-pointer">
                            </label>
                            <p class="text-[10px] text-slate-500 mt-1">Select an MP4, WebM, or MKV movie file (e.g. 2.0GB - 2.5GB) to replace or upload.</p>
                        </div>

                        <!-- Live Upload Progress Bar -->
                        <div x-show="uploading" x-cloak class="space-y-2 p-3 rounded-xl bg-slate-950 border border-amber-500/50">
                            <div class="flex items-center justify-between text-xs font-semibold">
                                <div class="flex items-center gap-2 text-white truncate max-w-xs sm:max-w-md">
                                    <svg class="animate-spin h-3.5 w-3.5 text-amber-400 flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="truncate" x-text="uploadFileName"></span>
                                    <span class="text-[10px] text-slate-400" x-text="'(' + uploadFileSize + ')'"></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] text-slate-400 font-mono" x-text="uploadSpeed"></span>
                                    <span class="text-amber-400 font-bold text-xs" x-text="uploadProgress + '%'"></span>
                                </div>
                            </div>
                            <div class="h-2 w-full rounded-full bg-slate-800 overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full transition-all duration-200"
                                     :style="'width: ' + uploadProgress + '%'"></div>
                            </div>
                            <p class="text-[10px] text-slate-400">Streaming directly to Cloudflare R2 bucket... Please keep this page open.</p>
                        </div>

                        <!-- Upload Success Alert -->
                        <div x-show="uploadSuccess" x-cloak class="p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/50 text-xs flex items-center justify-between">
                            <div class="flex items-center gap-2 text-emerald-300 font-semibold">
                                <x-bi-check-circle-fill class="h-4 w-4 text-emerald-400 flex-shrink-0" />
                                <span>Uploaded to Cloudflare R2! Streaming link populated below.</span>
                            </div>
                            <button type="button" @click="uploadSuccess = false; uploading = false;" class="text-[10px] text-emerald-400 hover:underline font-bold">
                                Upload a different file
                            </button>
                        </div>

                        <!-- Upload Error Alert -->
                        <div x-show="uploadError" x-cloak class="p-3 rounded-xl bg-red-500/20 border border-red-500/50 text-xs text-red-300 space-y-1">
                            <div class="flex items-center gap-2 font-bold text-red-400">
                                <x-bi-exclamation-triangle-fill class="h-4 w-4" />
                                <span>Upload Failed</span>
                            </div>
                            <p class="text-[11px]" x-text="uploadError"></p>
                        </div>
                    </div>

                    <!-- Option 2: Cloud / CDN Stream URL (Auto-filled by R2 or manual paste) -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-lg bg-blue-500/20 text-blue-400 text-xs">
                                    <x-bi-link-45deg class="h-3.5 w-3.5" />
                                </span>
                                <span class="text-xs font-bold text-white">Cloud / CDN Stream URL</span>
                            </div>
                            <span class="text-[10px] text-slate-400">
                                Automatically filled by R2 upload above
                            </span>
                        </div>
                        <input type="url"
                               name="video_url"
                               x-model="videoUrl"
                               placeholder="https://...r2.cloudflarestorage.com/movies/... or custom CDN domain"
                               class="w-full rounded-xl bg-white border border-slate-300 p-2.5 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Poster Artwork & Backdrop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Poster Image File</label>
                    @if ($movie->poster)
                        <img src="{{ $movie->posterUrl() }}" alt="Current Poster" class="h-20 w-14 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="poster_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="poster_url" value="{{ old('poster_url', $movie->poster) }}" placeholder="Or paste image URL" class="mt-2 w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Backdrop Banner File</label>
                    @if ($movie->backdrop)
                        <img src="{{ $movie->backdropUrl() }}" alt="Current Backdrop" class="h-20 w-32 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="backdrop_url" value="{{ old('backdrop_url', $movie->backdrop) }}" placeholder="Or paste banner URL" class="mt-2 w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Genres Multi-Select -->
            @php $currentGenreIds = $movie->genres->pluck('id')->toArray(); @endphp
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Genres</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                    @foreach ($genres as $genre)
                        <label class="flex items-center space-x-2 rounded-lg bg-slate-950 border border-slate-800 p-2.5 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" name="genres[]" value="{{ $genre->id }}" class="rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-500" {{ in_array($genre->id, old('genres', $currentGenreIds)) ? 'checked' : '' }}>
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
                        <option value="published" {{ old('status', $movie->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status', $movie->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $movie->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $movie->featured) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="featured" class="text-xs font-bold text-slate-300">Feature on Hero Banner</label>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="trending" id="trending" value="1" {{ old('trending', $movie->trending) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="trending" class="text-xs font-bold text-slate-300">Mark as Trending</label>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.movies.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 text-xs font-bold text-slate-300 hover:bg-slate-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                    Update Movie
                </button>
            </div>
        </form>
    </div>

    <script>
    function movieEditForm() {
        return {
            videoUrl: @json(old('video_url', $movie->video_url ?? '')),
            uploading: false,
            uploadProgress: 0,
            uploadSpeed: '',
            uploadFileName: '',
            uploadFileSize: '',
            uploadError: '',
            uploadSuccess: false,
            uploadVideoToR2(event) {
                const file = event.target.files[0];
                if (!file) return;

                this.uploading = true;
                this.uploadProgress = 0;
                this.uploadError = '';
                this.uploadSuccess = false;
                this.uploadFileName = file.name;
                this.uploadFileSize = (file.size / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
                if (file.size < 1024 * 1024 * 1024) {
                    this.uploadFileSize = (file.size / (1024 * 1024)).toFixed(1) + ' MB';
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const startTime = Date.now();

                fetch('{{ route('admin.media.generate-upload-url') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        filename: file.name,
                        content_type: file.type || 'video/mp4',
                        folder: 'movies'
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.upload_url) {
                        throw new Error(data.message || 'Failed to generate secure upload URL.');
                    }

                    const xhr = new XMLHttpRequest();
                    xhr.open('PUT', data.upload_url, true);
                    if (file.type) {
                        xhr.setRequestHeader('Content-Type', file.type);
                    }

                    xhr.upload.onprogress = (e) => {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            this.uploadProgress = percent;
                            const elapsedSec = (Date.now() - startTime) / 1000;
                            if (elapsedSec > 0.5) {
                                const bytesPerSec = e.loaded / elapsedSec;
                                this.uploadSpeed = (bytesPerSec / (1024 * 1024)).toFixed(1) + ' MB/s';
                            }
                        }
                    };

                    xhr.onload = () => {
                        if (xhr.status === 200 || xhr.status === 204) {
                            this.uploading = false;
                            this.uploadSuccess = true;
                            this.uploadProgress = 100;
                            this.videoUrl = data.public_url;
                        } else {
                            this.uploading = false;
                            this.uploadError = 'Upload to Cloudflare R2 returned status ' + xhr.status + '. Ensure your R2 bucket CORS allows PUT requests.';
                        }
                    };

                    xhr.onerror = () => {
                        this.uploading = false;
                        this.uploadError = 'Connection failed. Ensure Cloudflare R2 bucket CORS policy allows PUT from this origin.';
                    };

                    xhr.send(file);
                })
                .catch(err => {
                    this.uploading = false;
                    this.uploadError = err.message || 'Failed to request upload signature.';
                });
            }
        };
    }
    </script>
</x-admin-layout>

