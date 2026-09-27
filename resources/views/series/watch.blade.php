<x-layout>
    <x-slot:title>Watch {{ $series->title }} S{{ $season->season_number }}:E{{ $episode->episode_number }} — VJFlix Uganda</x-slot:title>

    <x-header />

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 space-y-6">
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center space-x-2 text-xs text-slate-400">
            <a href="/series" class="hover:text-amber-400 transition-colors">Series</a>
            <span>/</span>
            <a href="{{ route('series.show', $series->slug) }}" class="hover:text-amber-400 transition-colors">{{ $series->title }}</a>
            <span>/</span>
            <span class="text-amber-400 font-bold">Season {{ $season->season_number }}, Episode {{ $episode->episode_number }}</span>
        </nav>

        <!-- Video Player Viewport Container -->
        <div class="relative w-full rounded-2xl overflow-hidden bg-black border border-slate-800 shadow-2xl aspect-video max-h-[70vh]">
            <video 
                id="vjflix-player" 
                controls 
                preload="metadata"
                poster="{{ $episode->thumbnailUrl() }}" 
                class="w-full h-full object-contain">
                <source src="{{ $episode->streamUrl() }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>

        <!-- Episode Controls & Meta Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-800">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="rounded bg-amber-500 px-2.5 py-0.5 text-xs font-black uppercase text-black">
                        S{{ $season->season_number }} · E{{ $episode->episode_number }}
                    </span>
                    @if ($episode->vj)
                        <a href="/vjs/{{ $episode->vj->slug }}" class="inline-flex items-center gap-1 rounded-full bg-slate-800 border border-slate-700 px-3 py-0.5 text-xs font-bold text-amber-400 hover:bg-slate-700 transition-colors">
                            <x-bi-mic-fill class="h-3 w-3" />
                            Voiced by {{ $episode->vj->stage_name }}
                        </a>
                    @endif
                    <span class="text-xs text-slate-400">{{ $episode->durationFormatted() }}</span>
                    <span class="text-xs text-slate-400">• {{ number_format($episode->views) }} plays</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white font-display">
                    {{ $episode->title }}
                </h1>
                <p class="text-xs text-slate-400 mt-1">From <span class="text-slate-200 font-semibold">{{ $series->title }}</span></p>
            </div>

            <!-- Next / Prev Binge Buttons -->
            <div class="flex items-center gap-3">
                @if ($prevEpisode)
                    <a href="{{ route('series.watch', [$series->slug, $season->season_number, $prevEpisode->episode_number]) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-xs font-bold text-slate-200 hover:bg-slate-800 transition-colors">
                        <x-bi-skip-backward-fill class="h-4 w-4 text-amber-400" />
                        Previous (E{{ $prevEpisode->episode_number }})
                    </a>
                @endif

                @if ($nextEpisode)
                    <a href="{{ route('series.watch', [$series->slug, $season->season_number, $nextEpisode->episode_number]) }}" class="inline-flex items-center gap-1.5 px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all hover:scale-105">
                        Next Episode (E{{ $nextEpisode->episode_number }})
                        <x-bi-skip-forward-fill class="h-4 w-4" />
                    </a>
                @else
                    <a href="{{ route('series.show', $series->slug) }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-800 text-xs font-bold text-amber-400 hover:bg-slate-700 transition-colors">
                        <x-bi-list-ul class="h-4 w-4" />
                        All Episodes
                    </a>
                @endif
            </div>
        </div>

        <!-- Two Column Layout: Synopsis + Season Episodes Drawer -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Synopsis & Details -->
            <div class="lg:col-span-2 space-y-4">
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Episode Overview
                </h3>
                <p class="text-sm text-slate-300 leading-relaxed">
                    {{ $episode->overview ?: 'Official Luganda translated episode voiced by ' . ($episode->vj ? $episode->vj->stage_name : 'Ugandan VJ') . ' with full commentary and cultural adaptation.' }}
                </p>

                <div class="mt-6 rounded-2xl bg-slate-900/60 border border-slate-800 p-5 space-y-3">
                    <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">About This Series</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $series->synopsis ?: $series->description }}</p>
                    <div class="pt-2 flex items-center gap-4 text-xs text-slate-400">
                        <span>Original Air Year: <strong class="text-slate-200">{{ $series->first_air_year }}</strong></span>
                        <span>Rating: <strong class="text-amber-400">★ {{ number_format($series->average_rating, 1) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Right: Episodes List in this Season -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                        Season {{ $season->season_number }} Episodes
                    </h3>
                    <span class="text-xs text-slate-500">{{ $season->episodes->count() }} episodes</span>
                </div>

                <div class="space-y-2.5 max-h-[500px] overflow-y-auto pr-1">
                    @foreach ($season->episodes as $ep)
                        <a href="{{ route('series.watch', [$series->slug, $season->season_number, $ep->episode_number]) }}" class="flex items-center gap-3 p-2.5 rounded-xl border transition-all {{ $ep->id === $episode->id ? 'bg-amber-500/10 border-amber-500/50 text-white' : 'bg-slate-900/80 border-slate-800 hover:border-slate-700 text-slate-300 hover:text-white' }}">
                            <div class="relative w-20 aspect-video rounded-lg overflow-hidden bg-slate-800 flex-shrink-0">
                                <img src="{{ $ep->thumbnailUrl() }}" alt="{{ $ep->title }}" class="h-full w-full object-cover">
                                @if ($ep->id === $episode->id)
                                    <div class="absolute inset-0 bg-amber-500/30 flex items-center justify-center">
                                        <x-bi-volume-up-fill class="h-4 w-4 text-amber-300 animate-pulse" />
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-[10px] font-bold uppercase {{ $ep->id === $episode->id ? 'text-amber-400' : 'text-slate-400' }}">
                                        Episode {{ $ep->episode_number }}
                                    </span>
                                    <span class="text-[10px] text-slate-500">{{ $ep->durationFormatted() }}</span>
                                </div>
                                <p class="text-xs font-bold truncate mt-0.5">{{ $ep->title }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <x-footer />
</x-layout>
