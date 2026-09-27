<x-admin-layout>
    <x-slot:title>Manage Ugandan VJs — VJFlix CMS</x-slot:title>

    <div class="space-y-6 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-white font-display">
                    Ugandan Video Jockeys (VJs)
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Manage narrator profiles, ratings, stage names, and verification status.</p>
            </div>

            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.vjs.index') }}" class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ request('search') }}" 
                        placeholder="Search VJs..." 
                        class="w-48 sm:w-64 rounded-xl bg-slate-900 border border-slate-700 py-1.5 pl-3 pr-8 text-xs text-white placeholder-slate-400 focus:border-amber-500 focus:outline-none">
                    @if (request('search'))
                        <a href="{{ route('admin.vjs.index') }}" class="absolute right-2.5 top-2 text-slate-400 hover:text-white text-xs">✕</a>
                    @endif
                </form>

                <a href="{{ route('admin.vjs.create') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-md shadow-amber-500/20 transition-all">
                    <x-bi-plus-lg class="h-3.5 w-3.5 stroke-[2]" />
                    Add VJ
                </a>
            </div>
        </div>

        <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950/60 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3.5">Video Jockey</th>
                            <th class="px-6 py-3.5">Specialization</th>
                            <th class="px-6 py-3.5">Translations</th>
                            <th class="px-6 py-3.5">Rating</th>
                            <th class="px-6 py-3.5">Verified</th>
                            <th class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($vjs as $vj)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <img src="{{ $vj->avatarUrl() }}" alt="{{ $vj->stage_name }}" class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-800 bg-slate-800 flex-shrink-0">
                                        <div>
                                            <a href="/vjs/{{ $vj->slug }}" target="_blank" class="font-bold text-white hover:text-amber-400 transition-colors block">
                                                {{ $vj->stage_name }}
                                            </a>
                                            <span class="text-[11px] text-slate-400">{{ $vj->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-slate-300">
                                    {{ $vj->specialization }}
                                </td>
                                <td class="px-6 py-4 text-slate-300">
                                    <span class="font-bold text-white">{{ $vj->movies_count }}</span> movies · <span class="font-bold text-white">{{ $vj->series_count }}</span> series
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-amber-400 font-bold">★ {{ number_format($vj->rating, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($vj->is_verified)
                                        <span class="inline-flex items-center gap-1 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30 px-2 py-0.5 text-[10px] font-bold">
                                            <x-bi-check-lg class="h-3 w-3" />
                                            Verified
                                        </span>
                                    @else
                                        <span class="text-slate-500 text-[11px]">Unverified</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('admin.vjs.edit', $vj->id) }}" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold transition-colors">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.vjs.destroy', $vj->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this VJ?');">
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
                                    No Video Jockeys found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($vjs->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $vjs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
