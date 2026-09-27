@props([
    'type' => 'movie', // 'movie' or 'series'
    'id' => null,
    'inWatchlist' => false,
    'size' => 'md', // 'sm' or 'md' or 'lg'
    'iconOnly' => false,
])

@php
    $isIn = $inWatchlist || (auth()->check() && auth()->user()->watchlists()->where('watchable_type', $type === 'movie' ? \App\Models\Movie::class : \App\Models\Series::class)->where('watchable_id', $id)->exists());
    $btnClasses = $iconOnly 
        ? ($size === 'sm' ? 'p-1.5' : 'p-2.5')
        : match($size) {
            'sm' => 'px-3 py-1.5 text-xs',
            'lg' => 'px-6 py-3 text-base',
            default => 'px-4 py-2 text-sm',
        };
@endphp

<div x-data="{
    inList: {{ $isIn ? 'true' : 'false' }},
    loading: false,
    async toggle() {
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
            window.location.href = '{{ route('login') }}';
            return;
        }
        this.loading = true;
        try {
            const token = document.querySelector('meta[name=\'csrf-token\']')?.getAttribute('content');
            const res = await fetch('{{ route('watchlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    watchable_type: '{{ $type }}',
                    watchable_id: {{ $id }}
                })
            });
            const data = await res.json();
            if (data.status === 'success') {
                this.inList = data.in_watchlist;
            }
        } catch (e) {
            console.error('Watchlist toggle error:', e);
        } finally {
            this.loading = false;
        }
    }
}" class="inline-block">
    <button 
        type="button"
        @click="toggle()" 
        :disabled="loading"
        :class="inList ? 'bg-amber-500/20 text-amber-400 border border-amber-500/50 hover:bg-amber-500/30' : 'bg-neutral-800/80 text-white border border-neutral-700 hover:bg-neutral-700'"
        class="inline-flex items-center gap-2 rounded-lg font-medium transition-all duration-200 shadow-sm active:scale-95 disabled:opacity-50 {{ $btnClasses }}"
        title="Add to or remove from My List"
    >
        <template x-if="loading">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </template>
        <template x-if="!loading && inList">
            <svg class="h-4 w-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
        </template>
        <template x-if="!loading && !inList">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
        </template>
        @if(!$iconOnly)
            <span x-text="inList ? 'In My List' : 'Add to List'"></span>
        @endif
    </button>
</div>
