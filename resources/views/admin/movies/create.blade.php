<x-admin-layout>
    <x-slot:title>Add New Movie — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-4xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.movies.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Movies
                </a>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Add Movie Translation
                </h1>
            </div>
        </div>

        <!-- TMDB Quick Fetcher -->
        @include('admin.partials.tmdb-fetcher', ['type' => 'movie'])

        <form method="POST" action="{{ route('admin.movies.store') }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf

            <!-- Title & Release Year -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Movie Title *</label>
                    <input type="text" name="title" value="{{ old('title', $tmdbPrefill['translated_title_suggestion'] ?? ($tmdbPrefill['title'] ?? '')) }}" required placeholder="e.g. John Wick 4 (Luganda)" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Release Year</label>
                    <input type="number" name="release_year" value="{{ old('release_year', $tmdbPrefill['release_year'] ?? date('Y')) }}" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Original Title & Trailer URL -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Original English/Foreign Title</label>
                    <input type="text" name="original_title" value="{{ old('original_title', $tmdbPrefill['original_title'] ?? '') }}" placeholder="e.g. John Wick: Chapter 4" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Trailer URL (YouTube/MP4)</label>
                    <input type="url" name="trailer_url" value="{{ old('trailer_url', $tmdbPrefill['trailer_url'] ?? '') }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                    <input type="number" name="duration" value="{{ old('duration', $tmdbPrefill['duration'] ?? 120) }}" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
            </div>

            <!-- Synopsis & Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Short Synopsis</label>
                <textarea name="synopsis" rows="2" placeholder="Brief summary of the movie..." class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('synopsis', $tmdbPrefill['synopsis'] ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Description</label>
                <textarea name="description" rows="4" placeholder="Detailed plot and VJ translation notes..." class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('description', $tmdbPrefill['description'] ?? '') }}</textarea>
            </div>

            <!-- Video Stream URL -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Video Stream URL</label>
                <input type="url" name="video_url" value="{{ old('video_url') }}" placeholder="https://domain.com/streams/movie.mp4 or HLS m3u8 stream" class="w-full rounded-xl bg-white border border-slate-300 p-3 text-sm text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>

            <!-- Poster Artwork & Backdrop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Poster Image File</label>
                    <input type="file" name="poster_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    <p class="text-[10px] text-slate-500 mt-1">Or provide image URL below:</p>
                    <input type="url" name="poster_url" value="{{ old('poster_url', $tmdbPrefill['poster_url'] ?? '') }}" placeholder="https://..." class="mt-1 w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Backdrop Banner File</label>
                    <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    <p class="text-[10px] text-slate-500 mt-1">Or provide banner URL below:</p>
                    <input type="url" name="backdrop_url" value="{{ old('backdrop_url', $tmdbPrefill['backdrop_url'] ?? '') }}" placeholder="https://..." class="mt-1 w-full rounded-lg bg-white border border-slate-300 p-2 text-xs text-black font-semibold placeholder-slate-400 focus:border-amber-500 focus:outline-none">
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
