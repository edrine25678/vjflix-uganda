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

        <form method="POST" action="{{ route('admin.movies.update', $movie->id) }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf
            @method('PUT')

            <!-- Title & Release Year -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Movie Title *</label>
                    <input type="text" name="title" value="{{ old('title', $movie->title) }}" required class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Release Year</label>
                    <input type="number" name="release_year" value="{{ old('release_year', $movie->release_year) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- VJ & Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ugandan Video Jockey (VJ)</label>
                    <select name="vj_id" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
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
                    <input type="number" name="duration" value="{{ old('duration', $movie->duration) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- Synopsis & Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Short Synopsis</label>
                <textarea name="synopsis" rows="2" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('synopsis', $movie->synopsis) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Description</label>
                <textarea name="description" rows="4" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('description', $movie->description) }}</textarea>
            </div>

            <!-- Video Stream URL -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Video Stream URL</label>
                <input type="url" name="video_url" value="{{ old('video_url', $movie->video_url) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
            </div>

            <!-- Poster Artwork & Backdrop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Poster Image File</label>
                    @if ($movie->poster)
                        <img src="{{ $movie->posterUrl() }}" alt="Current Poster" class="h-20 w-14 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="poster_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="poster_url" value="{{ old('poster_url', $movie->poster) }}" placeholder="Or paste image URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Backdrop Banner File</label>
                    @if ($movie->backdrop)
                        <img src="{{ $movie->backdropUrl() }}" alt="Current Backdrop" class="h-20 w-32 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="backdrop_url" value="{{ old('backdrop_url', $movie->backdrop) }}" placeholder="Or paste banner URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
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
                    <select name="status" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
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
</x-admin-layout>
