<header class="fixed top-0 z-50 w-full bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80 text-white">
    <div class="w-full flex items-center justify-between px-3 sm:px-6 lg:px-8 py-2.5">
        <!-- Brand Logo (Pinned to Left Corner) -->
        <div class="flex items-center space-x-5">
            <a href="{{ auth()->check() ? route('vjflix.index') : '/' }}" class="flex items-center space-x-2 group">
                <img src="{{ asset('img/vjflix-icon.png') }}" alt="VJFlix" class="h-10 w-10 rounded-xl shadow-lg shadow-amber-500/25 group-hover:scale-105 transition-transform object-cover flex-shrink-0">
                <div class="flex flex-col">
                    <span class="text-xl font-extrabold tracking-wider text-white flex items-center gap-1 font-display">
                        FLIX <span class="text-[10px] uppercase font-bold tracking-widest px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">Uganda</span>
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center space-x-1 text-sm font-medium text-slate-300">
                <a href="{{ route('vjflix.index') }}" class="px-3 py-1.5 rounded-lg hover:text-white hover:bg-slate-800/60 transition-colors {{ request()->routeIs('vjflix.index') ? 'text-amber-400 bg-slate-800/80 font-semibold' : '' }}">
                    Movies
                </a>
                <a href="/series" class="px-3 py-1.5 rounded-lg hover:text-white hover:bg-slate-800/60 transition-colors {{ request()->is('series*') ? 'text-amber-400 bg-slate-800/80 font-semibold' : '' }}">
                    Series
                </a>
                <a href="/vjs" class="px-3 py-1.5 rounded-lg hover:text-white hover:bg-slate-800/60 transition-colors {{ request()->is('vjs*') ? 'text-amber-400 bg-slate-800/80 font-semibold' : '' }}">
                    Ugandan VJs
                </a>
                @auth
                    <a href="{{ route('watchlist.index') }}" class="px-3 py-1.5 rounded-lg hover:text-white hover:bg-slate-800/60 transition-colors {{ request()->routeIs('watchlist.*') ? 'text-amber-400 bg-slate-800/80 font-semibold' : '' }}">
                        My List
                    </a>
                @endauth
            </nav>
        </div>

        <!-- Right Side: Search & User Menu -->
        <div class="flex items-center space-x-3 sm:space-x-4">
            <div class="hidden sm:block">
                <livewire:search-vj-flix />
            </div>

            @auth
                <!-- User Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" type="button" class="flex items-center space-x-2 rounded-full p-1 ring-2 ring-slate-800 hover:ring-amber-500 transition-all focus:outline-none">
                        <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}">
                        <span class="hidden lg:block text-xs font-semibold text-slate-200 pr-1 max-w-[100px] truncate">
                            {{ auth()->user()->name }}
                        </span>
                        <x-bi-chevron-down class="h-3.5 w-3.5 text-slate-400 transition-transform duration-200" ::class="open ? 'rotate-180 text-amber-400' : ''" />
                    </button>

                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-slate-900 border border-slate-800 p-1.5 shadow-2xl text-xs text-slate-300 z-50">

                        <div class="px-3 py-2 border-b border-slate-800/80">
                            <p class="font-bold text-slate-100 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-amber-400 font-mono">@<span>{{ auth()->user()->username ?: 'viewer' }}</span> · {{ ucfirst(auth()->user()->role ?? 'user') }}</p>
                        </div>

                        <div class="py-1">
                            <a href="{{ route('watchlist.index') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('watchlist.*') ? 'text-amber-400 font-semibold' : '' }}">
                                <x-bi-bookmark class="h-4 w-4 mr-2 text-amber-400" />
                                My List & Saved
                            </a>
                            <a href="{{ route('profile.show') }}" class="flex items-center px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors {{ request()->routeIs('profile.*') ? 'text-amber-400 font-semibold' : '' }}">
                                <x-bi-gear class="h-4 w-4 mr-2 text-amber-400" />
                                Profile & Preferences
                            </a>
                            <a href="/vjs" class="flex items-center px-3 py-2 rounded-lg hover:bg-slate-800 hover:text-white transition-colors">
                                <x-bi-mic class="h-4 w-4 mr-2 text-amber-400" />
                                Browse Top VJs
                            </a>
                            @can('admin')
                                <a href="/admin" class="flex items-center px-3 py-2 rounded-lg text-amber-400 hover:bg-amber-500/10 font-semibold transition-colors">
                                    <x-bi-shield-lock class="h-4 w-4 mr-2" />
                                    Admin CMS Console
                                </a>
                            @endcan
                        </div>

                        <div class="border-t border-slate-800/80 pt-1">
                            <form action="/logout" method="post">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-3 py-2 rounded-lg text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 font-medium transition-colors">
                                    <x-bi-box-arrow-right class="h-4 w-4 mr-2" />
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center space-x-2">
                    <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-semibold text-slate-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 text-xs font-bold rounded-lg bg-amber-500 hover:bg-amber-400 text-black shadow-md shadow-amber-500/20 transition-all">
                        Join VJFlix
                    </a>
                </div>
            @endauth
        </div>
    </div>
</header>
<div class="h-16"></div> {{-- Spacer for fixed header --}}
