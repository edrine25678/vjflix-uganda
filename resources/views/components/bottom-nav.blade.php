<nav class="fixed bottom-0 inset-x-0 z-40 md:hidden bg-slate-950/95 backdrop-blur-xl border-t border-slate-800/90 px-2 py-1.5 shadow-[0_-4px_20px_rgba(0,0,0,0.6)]">
    <div class="grid grid-cols-5 items-center justify-around text-center">
        <!-- Movies / Home -->
        <a href="{{ route('vjflix.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-1 transition-colors {{ request()->routeIs('vjflix.index') || request()->routeIs('home') || request()->routeIs('movies.*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <x-bi-film class="h-5 w-5 mb-0.5 {{ request()->routeIs('vjflix.index') || request()->routeIs('home') || request()->routeIs('movies.*') ? 'text-amber-400' : 'text-slate-400' }}" />
            <span class="text-[10px] tracking-tight">Movies</span>
        </a>

        <!-- Series -->
        <a href="{{ route('series.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-1 transition-colors {{ request()->is('series*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <x-bi-tv class="h-5 w-5 mb-0.5 {{ request()->is('series*') ? 'text-amber-400' : 'text-slate-400' }}" />
            <span class="text-[10px] tracking-tight">Series</span>
        </a>

        <!-- Ugandan VJs -->
        <a href="{{ route('vjs.index') }}" 
           class="flex flex-col items-center justify-center py-1 px-1 transition-colors {{ request()->is('vjs*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <x-bi-mic-fill class="h-5 w-5 mb-0.5 {{ request()->is('vjs*') ? 'text-amber-400' : 'text-slate-400' }}" />
            <span class="text-[10px] tracking-tight">VJs</span>
        </a>

        <!-- Search -->
        <a href="{{ route('vjflix.index') }}#search" 
           class="flex flex-col items-center justify-center py-1 px-1 transition-colors text-slate-400 hover:text-amber-400">
            <x-bi-search class="h-5 w-5 mb-0.5" />
            <span class="text-[10px] tracking-tight">Search</span>
        </a>

        <!-- My List / Downloads -->
        <a href="{{ auth()->check() ? route('watchlist.index') : route('login') }}" 
           class="flex flex-col items-center justify-center py-1 px-1 transition-colors {{ request()->routeIs('watchlist.*') ? 'text-amber-400 font-bold' : 'text-slate-400 hover:text-slate-200' }}">
            <x-bi-bookmark-heart class="h-5 w-5 mb-0.5 {{ request()->routeIs('watchlist.*') ? 'text-amber-400' : 'text-slate-400' }}" />
            <span class="text-[10px] tracking-tight">My List</span>
        </a>
    </div>
</nav>
