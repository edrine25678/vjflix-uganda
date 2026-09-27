<x-layout>
    <x-slot:title>{{ $vj->stage_name }} Movies & Translations — VJFlix Uganda</x-slot:title>

    <x-header />

    <!-- VJ Profile Banner Header -->
    <div class="relative bg-slate-900 border-b border-slate-800">
        <!-- Backdrop Cover -->
        <div class="h-64 sm:h-80 w-full relative overflow-hidden bg-slate-950">
            <img src="{{ $vj->coverUrl() }}" alt="{{ $vj->stage_name }}" class="h-full w-full object-cover opacity-25 filter blur-sm">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/60 to-transparent"></div>
        </div>

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative -mt-24 sm:-mt-32 pb-8 flex flex-col md:flex-row items-center md:items-end gap-6 text-center md:text-left">
                <!-- Avatar -->
                <div class="relative flex-shrink-0">
                    <img src="{{ $vj->avatarUrl() }}" alt="{{ $vj->stage_name }}" class="h-36 w-36 sm:h-44 sm:w-44 rounded-3xl object-cover ring-4 ring-amber-500/60 shadow-2xl bg-slate-800">
                    @if ($vj->is_verified)
                        <span class="absolute bottom-2 right-2 rounded-full bg-amber-500 p-1.5 text-black shadow-lg" title="Verified Ugandan VJ">
                            <x-bi-check-lg class="h-4 w-4 stroke-[3]" />
                        </span>
                    @endif
                </div>

                <!-- Info -->
                <div class="flex-grow">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 mb-1">
                        <span class="rounded bg-amber-500/10 border border-amber-500/30 px-2.5 py-0.5 text-xs font-bold text-amber-400">
                            {{ $vj->specialization }}
                        </span>
                        <span class="rounded bg-slate-800 px-2 py-0.5 text-xs font-semibold text-slate-300">
                            ★ {{ number_format($vj->rating, 2) }} Rating
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight font-display">
                        {{ $vj->stage_name }}
                    </h1>
                    <p class="text-sm text-slate-400 font-medium">Real Name: {{ $vj->name }}</p>

                    <p class="mt-3 max-w-2xl text-xs sm:text-sm text-slate-300 leading-relaxed">
                        {{ $vj->biography }}
                    </p>

                    <!-- Stats Bar -->
                    <div class="mt-4 flex flex-wrap items-center justify-center md:justify-start gap-6 text-xs text-slate-400 pt-3 border-t border-slate-800">
                        <div>
                            <span class="font-extrabold text-white text-base">{{ $movies->total() }}</span>
                            <span class="ml-1 text-slate-400">Translated Movies</span>
                        </div>
                        <div>
                            <span class="font-extrabold text-white text-base">{{ number_format($vj->views_count) }}</span>
                            <span class="ml-1 text-slate-400">Total Stream Plays</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Movies Catalog Translated by This VJ -->
    <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white flex items-center gap-2">
                    <span class="w-1.5 h-6 rounded-full bg-amber-500 inline-block"></span>
                    Movies Translated by {{ $vj->stage_name }}
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">Stream official Luganda audio translated master releases</p>
            </div>
        </div>

        @if ($movies->isNotEmpty())
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                @foreach ($movies as $movie)
                    <div class="w-full !mr-0">
                        <x-velflix-card :movie="$movie" />
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $movies->links() }}
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-slate-800 p-12 text-center text-slate-400">
                <x-bi-film class="mx-auto h-12 w-12 text-slate-600 mb-3" />
                <h3 class="text-base font-bold text-slate-200">No movies uploaded for this VJ yet</h3>
                <p class="text-xs text-slate-500 mt-1">Check back soon for new translation releases from {{ $vj->stage_name }}.</p>
            </div>
        @endif
    </main>

    <x-footer />
</x-layout>
