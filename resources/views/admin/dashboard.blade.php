<x-admin-layout>
    <x-slot:title>Dashboard — VJFlix CMS</x-slot:title>

    <div class="space-y-8 max-w-7xl mx-auto">
        <!-- Header & Quick Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-display">
                    Platform Overview
                </h1>
                <p class="text-xs text-slate-400 mt-1">Manage movies, translated series, Ugandan VJs, and streaming catalog.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('admin.movies.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all hover:scale-105">
                    <x-bi-plus-lg class="h-3.5 w-3.5 stroke-[2]" />
                    Add Movie
                </a>
                <a href="{{ route('admin.series.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-all">
                    <x-bi-plus-lg class="h-3.5 w-3.5 text-amber-400 stroke-[2]" />
                    Add Series
                </a>
                <a href="{{ route('admin.vjs.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-all">
                    <x-bi-plus-lg class="h-3.5 w-3.5 text-amber-400 stroke-[2]" />
                    Add VJ
                </a>
            </div>
        </div>

        <!-- Metrics Stat Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Movies</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white mt-1 block font-display">{{ $stats['total_movies'] }}</span>
                <span class="text-[10px] text-amber-400 mt-1 block">Translated</span>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TV Series</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white mt-1 block font-display">{{ $stats['total_series'] }}</span>
                <span class="text-[10px] text-amber-400 mt-1 block">Multi-Season</span>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Episodes</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white mt-1 block font-display">{{ $stats['total_episodes'] }}</span>
                <span class="text-[10px] text-slate-400 mt-1 block">Full Stream</span>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Ugandan VJs</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-white mt-1 block font-display">{{ $stats['total_vjs'] }}</span>
                <span class="text-[10px] text-amber-400 mt-1 block">Narrators</span>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Movie Views</span>
                <span class="text-xl sm:text-2xl font-extrabold text-white mt-1 block font-display truncate">{{ number_format($stats['total_movie_views']) }}</span>
                <span class="text-[10px] text-emerald-400 mt-1 block">Plays</span>
            </div>

            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Series Views</span>
                <span class="text-xl sm:text-2xl font-extrabold text-white mt-1 block font-display truncate">{{ number_format($stats['total_series_views']) }}</span>
                <span class="text-[10px] text-emerald-400 mt-1 block">Plays</span>
            </div>
        </div>

        <!-- Two Column Recent Lists -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Movies -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                        Recent Movies
                    </h2>
                    <a href="{{ route('admin.movies.index') }}" class="text-xs font-bold text-amber-400 hover:underline">View All &rarr;</a>
                </div>

                <div class="divide-y divide-slate-800">
                    @forelse ($recentMovies as $movie)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center space-x-3 min-w-0">
                                <img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" class="h-10 w-8 rounded object-cover bg-slate-800 flex-shrink-0">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">{{ $movie->title }}</h4>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $movie->vj ? $movie->vj->stage_name : 'No VJ' }} · {{ $movie->release_year }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.movies.edit', $movie->id) }}" class="text-xs font-semibold text-slate-300 hover:text-amber-400 px-2 py-1 rounded bg-slate-800 hover:bg-slate-700">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-500">No movies found.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Series -->
            <div class="rounded-2xl bg-slate-900 border border-slate-800 p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                        Recent TV Series
                    </h2>
                    <a href="{{ route('admin.series.index') }}" class="text-xs font-bold text-amber-400 hover:underline">View All &rarr;</a>
                </div>

                <div class="divide-y divide-slate-800">
                    @forelse ($recentSeries as $s)
                        <div class="py-3 flex items-center justify-between gap-4">
                            <div class="flex items-center space-x-3 min-w-0">
                                <img src="{{ $s->posterUrl() }}" alt="{{ $s->title }}" class="h-10 w-8 rounded object-cover bg-slate-800 flex-shrink-0">
                                <div class="min-w-0">
                                    <h4 class="text-xs font-bold text-white truncate">{{ $s->title }}</h4>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $s->vj ? $s->vj->stage_name : 'No VJ' }} · {{ $s->first_air_year }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.series.edit', $s->id) }}" class="text-xs font-semibold text-slate-300 hover:text-amber-400 px-2 py-1 rounded bg-slate-800 hover:bg-slate-700">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="py-4 text-xs text-slate-500">No series found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
