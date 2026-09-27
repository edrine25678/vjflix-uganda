<x-admin-layout>
    <x-slot:title>Manage {{ $series->title }} — VJFlix CMS</x-slot:title>

    <div class="space-y-10 max-w-5xl mx-auto">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('admin.series.index') }}" class="text-xs text-amber-400 hover:underline flex items-center gap-1 mb-1">
                    &larr; Back to Series
                </a>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Manage: {{ $series->title }}
                </h1>
            </div>
            <a href="{{ route('series.show', $series->slug) }}" target="_blank" class="text-xs font-bold text-amber-400 hover:underline">
                View Live &rarr;
            </a>
        </div>

        <!-- 1. Series General Metadata Form -->
        <form method="POST" action="{{ route('admin.series.update', $series->id) }}" enctype="multipart/form-data" class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-6 shadow-xl">
            @csrf
            @method('PUT')

            <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                Series Details
            </h2>

            <!-- Title & Air Year -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Series Title *</label>
                    <input type="text" name="title" value="{{ old('title', $series->title) }}" required class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">First Air Year</label>
                    <input type="number" name="first_air_year" value="{{ old('first_air_year', $series->first_air_year) }}" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                </div>
            </div>

            <!-- VJ Selection -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Ugandan Video Jockey (VJ)</label>
                <select name="vj_id" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">
                    <option value="">-- Select Translating VJ --</option>
                    @foreach ($vjs as $vj)
                        <option value="{{ $vj->id }}" {{ old('vj_id', $series->vj_id) == $vj->id ? 'selected' : '' }}>
                            {{ $vj->stage_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Synopsis & Description -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Short Synopsis</label>
                <textarea name="synopsis" rows="2" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('synopsis', $series->synopsis) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Full Description</label>
                <textarea name="description" rows="3" class="w-full rounded-xl bg-slate-950 border border-slate-700 p-3 text-sm text-white focus:border-amber-500 focus:outline-none">{{ old('description', $series->description) }}</textarea>
            </div>

            <!-- Poster Artwork & Backdrop -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-xl bg-slate-950 border border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Series Poster</label>
                    @if ($series->poster)
                        <img src="{{ $series->posterUrl() }}" alt="Poster" class="h-20 w-14 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="poster_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="poster_url" value="{{ old('poster_url', $series->poster) }}" placeholder="Or paste poster URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1">Backdrop Banner</label>
                    @if ($series->backdrop)
                        <img src="{{ $series->backdropUrl() }}" alt="Backdrop" class="h-20 w-32 object-cover rounded mb-2 border border-slate-700">
                    @endif
                    <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400">
                    <input type="url" name="backdrop_url" value="{{ old('backdrop_url', $series->backdrop) }}" placeholder="Or paste backdrop URL" class="mt-2 w-full rounded-lg bg-slate-900 border border-slate-700 p-2 text-xs text-white">
                </div>
            </div>

            <!-- Genres -->
            @php $currentGenreIds = $series->genres->pluck('id')->toArray(); @endphp
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
                        <option value="published" {{ old('status', $series->status) === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="draft" {{ old('status', $series->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $series->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="featured" id="featured" value="1" {{ old('featured', $series->featured) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="featured" class="text-xs font-bold text-slate-300">Feature on Hero</label>
                </div>

                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="trending" id="trending" value="1" {{ old('trending', $series->trending) ? 'checked' : '' }} class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                    <label for="trending" class="text-xs font-bold text-slate-300">Mark as Trending</label>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                    Update Series Details
                </button>
            </div>
        </form>

        <!-- 2. Seasons & Episodes Management Section -->
        <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 sm:p-8 space-y-8 shadow-xl">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h2 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-5 rounded-full bg-amber-500 inline-block"></span>
                        Seasons & Episodes Manager
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Manage seasons and add streamable episodes with video links.</p>
                </div>
            </div>

            @foreach ($series->seasons as $season)
                <div class="rounded-xl bg-slate-950 border border-slate-800/80 p-5 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="rounded bg-amber-500 px-2 py-0.5 text-xs font-black text-black">
                                Season {{ $season->season_number }}
                            </span>
                            <h3 class="text-sm font-bold text-white">{{ $season->title ?: 'Season ' . $season->season_number }}</h3>
                            <span class="text-xs text-slate-400">({{ $season->episodes->count() }} episodes)</span>
                        </div>
                    </div>

                    <!-- Episodes Table in this Season -->
                    @if ($season->episodes->isNotEmpty())
                        <div class="divide-y divide-slate-800/60 border border-slate-800/80 rounded-xl overflow-hidden">
                            @foreach ($season->episodes as $episode)
                                <div class="p-3 flex items-center justify-between hover:bg-slate-900/60 transition-colors text-xs">
                                    <div class="flex items-center space-x-3 min-w-0">
                                        <span class="font-mono text-amber-400 font-bold w-7 text-center">E{{ $episode->episode_number }}</span>
                                        <div class="min-w-0">
                                            <span class="font-bold text-white truncate block">{{ $episode->title }}</span>
                                            <span class="text-[11px] text-slate-500 truncate block">
                                                {{ $episode->durationFormatted() }} · {{ $episode->video_url ? 'Has Video Link' : 'No Video Link' }}
                                                @if ($episode->is_free) · <strong class="text-emerald-400">FREE</strong> @endif
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('series.watch', [$series->slug, $season->season_number, $episode->episode_number]) }}" target="_blank" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-[11px] font-semibold">
                                            Watch
                                        </a>
                                        <form method="POST" action="{{ route('admin.episodes.destroy', $episode->id) }}" onsubmit="return confirm('Delete episode {{ $episode->episode_number }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2 py-1 rounded bg-red-500/10 hover:bg-red-500/20 text-red-400 text-[11px] font-semibold">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-500 italic">No episodes added to this season yet.</p>
                    @endif

                    <!-- Add Episode to this Season Form -->
                    <div x-data="{ open: false }" class="pt-2">
                        <button @click="open = !open" type="button" class="text-xs font-bold text-amber-400 hover:underline flex items-center gap-1">
                            <x-bi-plus-circle class="h-3.5 w-3.5" />
                            <span x-text="open ? 'Close Episode Form' : '+ Add Episode to Season {{ $season->season_number }}'"></span>
                        </button>

                        <div x-show="open" x-cloak class="mt-4 p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300">
                                New Episode for Season {{ $season->season_number }}
                            </h4>

                            <form method="POST" action="{{ route('admin.episodes.store', $season->id) }}" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Episode # *</label>
                                        <input type="number" name="episode_number" value="{{ ($season->episodes->max('episode_number') ?? 0) + 1 }}" required class="w-full rounded-lg bg-slate-950 border border-slate-700 p-2 text-xs text-white">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Episode Title *</label>
                                        <input type="text" name="title" required placeholder="e.g. Ekitundu 4: Okulumba" class="w-full rounded-lg bg-slate-950 border border-slate-700 p-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Duration (min)</label>
                                        <input type="number" name="duration" value="45" class="w-full rounded-lg bg-slate-950 border border-slate-700 p-2 text-xs text-white">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Video Stream URL</label>
                                    <input type="url" name="video_url" placeholder="https://domain.com/streams/episode.mp4 or m3u8" class="w-full rounded-lg bg-slate-950 border border-slate-700 p-2 text-xs text-white">
                                </div>

                                <div>
                                    <label class="block text-[11px] font-bold uppercase text-slate-400 mb-1">Overview</label>
                                    <textarea name="overview" rows="2" placeholder="Summary of this translated episode..." class="w-full rounded-lg bg-slate-950 border border-slate-700 p-2 text-xs text-white"></textarea>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <label class="flex items-center space-x-2 text-xs text-slate-300">
                                        <input type="checkbox" name="is_free" value="1" class="rounded bg-slate-950 border-slate-700 text-amber-500 focus:ring-amber-500">
                                        <span>Free Preview Episode (Unlocked for guests)</span>
                                    </label>

                                    <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md">
                                        Save Episode
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-admin-layout>
