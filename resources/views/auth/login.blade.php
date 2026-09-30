<x-layout>
    <x-slot:title>Log In — VJFlix Uganda</x-slot:title>

    <x-header />

    <section class="min-h-[85vh] flex items-center justify-center px-4 py-12 relative overflow-hidden bg-slate-950">
        <!-- Ambient Radial Glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-amber-500/10 rounded-full filter blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-yellow-500/10 rounded-full filter blur-3xl pointer-events-none"></div>

        <main class="w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900/90 backdrop-blur-xl p-8 shadow-2xl relative z-10">
            <!-- Header Branding -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gradient-to-tr from-amber-500 to-yellow-400 text-black font-extrabold text-2xl font-display shadow-lg shadow-amber-500/20 mb-3">
                    VJ
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Welcome Back</h1>
                <p class="text-xs text-slate-400 mt-1">Sign in to watch your favorite Luganda translated movies & VJs</p>
            </div>

            <!-- Flash Error Message if any -->
            @if (session('error'))
                <div class="mb-6 rounded-xl bg-red-500/10 border border-red-500/30 p-3.5 text-xs text-red-400 flex items-center gap-2">
                    <x-bi-exclamation-triangle-fill class="h-4 w-4 flex-shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Google OAuth Button -->
            <div class="mb-6">
                <a href="{{ route('login.google') }}" 
                   class="w-full flex items-center justify-center gap-3 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold py-3 px-4 text-sm transition-all shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <svg class="h-5 w-5" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Continue with Google</span>
                </a>
            </div>

            <!-- Divider -->
            <div class="relative flex py-2 items-center mb-6">
                <div class="flex-grow border-t border-slate-800"></div>
                <span class="flex-shrink mx-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">or sign in with email</span>
                <div class="flex-grow border-t border-slate-800"></div>
            </div>

            <form method="POST" action="/login" class="space-y-5">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Email Address
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') }}" 
                        required 
                        autocomplete="email"
                        placeholder="yourname@example.com"
                        class="w-full rounded-xl bg-white text-black font-semibold placeholder-slate-400 border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">

                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Password
                        </label>
                    </div>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full rounded-xl bg-white text-black font-semibold placeholder-slate-400 border border-slate-300 px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all">

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full rounded-xl bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-black font-extrabold py-3.5 px-4 text-sm tracking-wide shadow-lg shadow-amber-500/20 transition-all transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-amber-500">
                    LOG IN TO VJFLIX
                </button>
            </form>

            <!-- Register Footer Link -->
            <div class="mt-8 text-center pt-6 border-t border-slate-800/80">
                <p class="text-xs text-slate-400">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-amber-400 hover:text-amber-300 hover:underline">
                        Register for Free
                    </a>
                </p>
            </div>
        </main>
    </section>

    <x-footer />
</x-layout>
