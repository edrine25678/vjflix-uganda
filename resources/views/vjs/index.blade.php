<x-layout>
    <x-slot:title>Ugandan Video Jockeys (VJs) — VJFlix Uganda</x-slot:title>

    <x-header />

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <!-- VJ Discovery Hero -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-amber-950/30 to-slate-900 border border-slate-800 p-8 sm:p-12 mb-10 text-center">
            <div class="absolute -top-24 -left-24 h-64 w-64 rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 h-64 w-64 rounded-full bg-yellow-500/10 blur-3xl"></div>

            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 px-3 py-1 text-xs font-bold text-amber-400 uppercase tracking-widest mb-4">
                <x-bi-mic-fill class="h-3 w-3" />
                The Masters of the Microphone
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight font-display">
                Uganda's Legendary <span class="text-amber-400">Video Jockeys</span>
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm sm:text-base text-slate-300">
                The iconic narrators who transform global blockbusters into Luganda masterclasses. Explore their translations, signature punchlines, and latest releases.
            </p>
        </div>

        <!-- VJs Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
            @forelse ($vjs as $vj)
                <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-slate-900/90 border border-slate-800 hover:border-amber-500/60 transition-all duration-300 shadow-xl hover:shadow-amber-500/10 hover:-translate-y-1">
                    <!-- Top Cover Banner -->
                    <div class="h-28 w-full bg-gradient-to-r from-amber-900/40 via-slate-800 to-slate-900 relative overflow-hidden">
                        <div class="absolute inset-0 bg-slate-950/40"></div>
                    </div>

                    <!-- Profile Photo & Header -->
                    <div class="relative px-6 pb-6 pt-0 flex flex-col flex-grow">
                        <div class="-mt-14 mb-4 flex items-end justify-between">
                            <div class="relative">
                                <img src="{{ $vj->avatarUrl() }}" alt="{{ $vj->stage_name }}" class="h-24 w-24 rounded-2xl object-cover ring-4 ring-slate-900 shadow-xl">
                                @if ($vj->is_verified)
                                    <span class="absolute -bottom-1 -right-1 rounded-full bg-amber-500 p-1 text-black shadow" title="Verified Ugandan VJ">
                                        <x-bi-check-lg class="h-3.5 w-3.5 stroke-[3]" />
                                    </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-1 rounded-lg bg-slate-800/80 px-2.5 py-1 text-xs font-bold text-amber-400 border border-slate-700">
                                <x-bi-star-fill class="h-3.5 w-3.5 text-amber-400" />
                                <span>{{ number_format($vj->rating, 2) }}</span>
                            </div>
                        </div>

                        <!-- Info -->
                        <div>
                            <h2 class="text-xl font-bold text-white group-hover:text-amber-400 transition-colors flex items-center gap-1.5">
                                {{ $vj->stage_name }}
                            </h2>
                            <p class="text-xs text-slate-400">{{ $vj->name }}</p>
                            
                            <div class="mt-2.5 inline-block rounded-md bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 text-[11px] font-semibold text-amber-300">
                                {{ $vj->specialization }}
                            </div>

                            <p class="mt-3 text-xs text-slate-300 line-clamp-3 leading-relaxed">
                                {{ $vj->biography }}
                            </p>
                        </div>

                        <!-- Stats Bar & Link -->
                        <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between mt-auto">
                            <div class="flex items-center space-x-4 text-xs text-slate-400">
                                <div>
                                    <span class="font-extrabold text-white text-sm">{{ $vj->movies_count }}</span>
                                    <span class="text-[10px] block text-slate-400">Movies</span>
                                </div>
                                <div>
                                    <span class="font-extrabold text-white text-sm">{{ number_format($vj->views_count) }}</span>
                                    <span class="text-[10px] block text-slate-400">Plays</span>
                                </div>
                            </div>

                            <a href="/vjs/{{ $vj->slug }}" class="inline-flex items-center rounded-lg bg-amber-500 hover:bg-amber-400 text-black px-3.5 py-1.5 text-xs font-bold shadow-md shadow-amber-500/20 transition-all group-hover:scale-105">
                                Browse VJ
                                <x-bi-arrow-right class="ml-1 h-3.5 w-3.5" />
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 text-slate-400">
                    <p class="text-lg">No Video Jockeys found.</p>
                </div>
            @endforelse
        </div>
    </main>

    <x-footer />
</x-layout>
