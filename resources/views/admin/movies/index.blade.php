<x-admin-layout>
    <x-slot:title>Manage Movies — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <!-- Title Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Movie Catalog
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Manage movies, translations, video links, and VJ attributions.</p>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.movies.index') }}" class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search movies or VJs..." 
                        class="w-48 sm:w-64 rounded-xl bg-slate-900 border border-slate-700 py-1.5 pl-3 pr-8 text-xs text-white placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                    @if (request('search'))
                        <a href="{{ route('admin.movies.index') }}" class="absolute right-2.5 top-2 text-slate-400 hover:text-white text-xs">✕</a>
                    @endif
                </form>

                <a href="{{ route('admin.movies.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all">
                    <x-bi-plus-lg class="h-3.5 w-3.5 stroke-[2]" />
                    Add Movie
                </a>
            </div>
        </div>

        <!-- Movies Table Card -->
        <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3.5">Movie</th>
                            <th class="px-6 py-3.5">Video Jockey</th>
                            <th class="px-6 py-3.5">Year / Length</th>
                            <th class="px-6 py-3.5">Views</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($movies as $movie)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $movie->posterUrl() }}" alt="{{ $movie->title }}" class="h-12 w-9 rounded-lg object-cover bg-slate-800 flex-shrink-0 shadow">
                                        <div>
                                            <a href="{{ route('movies.show', $movie->slug) }}" target="_blank" class="font-bold text-white hover:text-amber-400 transition-colors block">
                                                {{ $movie->title }}
                                            </a>
                                            <div class="flex items-center gap-1 mt-0.5">
                                                @foreach ($movie->genres->take(2) as $g)
                                                    <span class="text-[10px] text-slate-400">{{ $g->name }}</span>
                                                    @if (!$loop->last) <span class="text-slate-600">•</span> @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($movie->vj)
                                        <span class="inline-flex items-center gap-1 rounded bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[11px] font-bold text-amber-400">
                                            {{ $movie->vj->stage_name }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-slate-400">
                                    {{ $movie->release_year }} · {{ $movie->durationFormatted() }}
                                </td>
                                <td class="px-6 py-4 font-mono text-slate-300">
                                    {{ number_format($movie->views) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $movie->status === 'published' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $movie->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.movies.edit', $movie->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.movies.destroy', $movie->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this movie?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-red-500/10 hover:bg-red-500/20 text-red-400 text-xs font-semibold transition-colors">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    No movies found in the catalog.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movies->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $movies->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
