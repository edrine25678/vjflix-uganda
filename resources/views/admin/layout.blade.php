<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Admin CMS' }} — VJFlix Uganda</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Bebas+Neue&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-display { font-family: 'Bebas Neue', sans-serif; }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-950 text-slate-100 antialiased">

    <!-- Admin Sidebar Navigation -->
    <aside class="w-64 flex-shrink-0 bg-slate-900 border-r border-slate-800 flex flex-col justify-between hidden md:flex">
        <div>
            <!-- Admin Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-base font-display">
                        VJ
                    </span>
                    <span class="text-lg font-extrabold text-white tracking-wider font-display">
                        CMS <span class="text-xs px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-400 font-sans font-bold">Admin</span>
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="p-4 space-y-1 text-sm font-semibold">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-speedometer2 class="h-4 w-4 mr-3" />
                    Dashboard
                </a>

                <a href="{{ route('admin.movies.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.movies.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-film class="h-4 w-4 mr-3" />
                    Movies
                </a>

                <a href="{{ route('admin.series.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.series.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-tv class="h-4 w-4 mr-3" />
                    TV Series & Episodes
                </a>

                <a href="{{ route('admin.vjs.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.vjs.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-mic class="h-4 w-4 mr-3" />
                    Ugandan VJs
                </a>

                <a href="{{ route('admin.analytics.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.analytics.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-bar-chart class="h-4 w-4 mr-3" />
                    Analytics
                </a>
            </nav>

            <!-- Monetisation -->
            <nav class="px-4 pb-4 space-y-1 text-sm font-semibold">
                <p class="px-3 pt-2 pb-1 text-[10px] uppercase tracking-widest text-slate-500 font-bold">Monetisation</p>

                <a href="{{ route('admin.plans.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.plans.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-list-check class="h-4 w-4 mr-3" />
                    Subscription Plans
                </a>

                <a href="{{ route('admin.subscriptions.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.subscriptions.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-calendar-check class="h-4 w-4 mr-3" />
                    Subscriptions
                </a>

                <a href="{{ route('admin.payments.index') }}" class="flex items-center px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.payments.*') ? 'bg-amber-500 text-black font-extrabold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <x-bi-cash-stack class="h-4 w-4 mr-3" />
                    Mobile Money Payments
                </a>
            </nav>
        </div>

        <!-- Sidebar Footer -->
        <div class="p-4 border-t border-slate-800 space-y-2">
            <a href="{{ route('vjflix.index') }}" class="flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <x-bi-box-arrow-up-right class="h-3.5 w-3.5 mr-2 text-amber-400" />
                View Viewer Site
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-3 py-2 rounded-xl text-xs font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors">
                    <x-bi-box-arrow-right class="h-3.5 w-3.5 mr-2" />
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col overflow-hidden">
        <!-- Top Header -->
        <header class="h-16 flex-shrink-0 bg-slate-900/60 backdrop-blur border-b border-slate-800 flex items-center justify-between px-4 sm:px-6">
            <div class="flex items-center space-x-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    VJFlix CMS Admin Console
                </span>
            </div>

            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full object-cover ring-2 ring-amber-500">
                    <div class="hidden sm:block text-left text-xs">
                        <span class="block font-bold text-white leading-none">{{ auth()->user()->name }}</span>
                        <span class="text-[10px] text-amber-400 font-mono">{{ ucfirst(auth()->user()->role ?? 'admin') }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dynamic Body Content -->
        <main class="flex-grow overflow-y-auto p-4 sm:p-6 lg:p-8">
            <!-- Flash Message Alerts -->
            @if (session('success'))
                <div class="mb-6 flex items-center justify-between rounded-xl bg-emerald-500/10 border border-emerald-500/30 p-4 text-emerald-400 text-xs font-semibold">
                    <div class="flex items-center space-x-2">
                        <x-bi-check-circle-fill class="h-4 w-4 text-emerald-400 flex-shrink-0" />
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 p-4 text-red-400 text-xs font-semibold">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

</body>
</html>
