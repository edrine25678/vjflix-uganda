<x-layout>
    <section class="relative flex min-h-[calc(100vh-4rem)] items-center justify-center overflow-hidden px-6 py-16">
        {{-- Ambient brand glow, decorative only. --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div class="absolute left-1/2 top-0 h-96 w-96 -translate-x-1/2 rounded-full bg-amber-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 right-1/4 h-72 w-72 rounded-full bg-purple-600/10 blur-3xl"></div>
        </div>

        <main class="relative w-full max-w-md">
            {{-- Brand mark --}}
            <div class="flex flex-col items-center text-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-amber-500 bg-slate-900 shadow-lg shadow-amber-500/20">
                    <x-bi-film class="h-8 w-8 text-amber-500" />
                </div>

                <h1 class="mt-6 font-display text-4xl uppercase tracking-wide text-white sm:text-5xl">
                    VJFlix <span class="text-amber-500">Uganda</span>
                </h1>
                <p class="mt-2 text-[11px] uppercase tracking-[0.25em] text-slate-400">
                    Your Movies. Your VJs. Your Language.
                </p>
            </div>

            {{-- Card --}}
            <div class="mt-10 rounded-2xl border border-slate-800 bg-slate-900/60 p-6 shadow-2xl shadow-black/40 backdrop-blur sm:p-8">
                <h2 class="font-display text-2xl uppercase tracking-wide text-white">Log In</h2>
                <p class="mt-1 text-sm text-slate-400">
                    Welcome back. Sign in to continue watching.
                </p>

                {{-- Session / credential errors --}}
                @error('email')
                    <div class="mt-6 flex items-start gap-3 rounded-lg border border-red-500/30 bg-red-500/10 p-3" role="alert">
                        <x-bi-exclamation-triangle class="mt-0.5 h-4 w-4 shrink-0 text-red-400" />
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    </div>
                @enderror

                @error('password')
                    <div class="mt-6 flex items-start gap-3 rounded-lg border border-red-500/30 bg-red-500/10 p-3" role="alert">
                        <x-bi-exclamation-triangle class="mt-0.5 h-4 w-4 shrink-0 text-red-400" />
                        <p class="text-sm text-red-300">{{ $message }}</p>
                    </div>
                @enderror

                <form method="post" action="{{ route('login') }}" class="mt-6 space-y-5">
                    @csrf

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-200">
                            Email
                        </label>
                        <div class="relative mt-2">
                            <x-bi-envelope class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-500" />
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="email"
                                placeholder="you@example.com"
                                @error('email') aria-invalid="true" @enderror
                                class="w-full rounded-lg border border-slate-700 bg-slate-950 py-3 pl-11 pr-4 text-slate-100 placeholder-slate-500 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30 @error('email') border-red-500/60 @enderror"
                            >
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-200">
                            Password
                        </label>
                        <div class="relative mt-2" x-data="{ show: false }">
                            <input
                                id="password"
                                name="password"
                                x-bind:type="show ? 'text' : 'password'"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Enter your password"
                                @error('password') aria-invalid="true" @enderror
                                class="w-full rounded-lg border border-slate-700 bg-slate-950 py-3 pl-4 pr-12 text-slate-100 placeholder-slate-500 transition focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/30 @error('password') border-red-500/60 @enderror"
                            >
                            <button
                                type="button"
                                x-on:click="show = ! show"
                                x-bind:aria-label="show ? 'Hide password' : 'Show password'"
                                class="absolute right-1 top-1/2 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-800 hover:text-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                            >
                                <x-bi-eye x-show="! show" class="h-5 w-5" />
                                <x-bi-eye-slash x-show="show" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    {{-- Remember me --}}
                    <div class="flex items-center gap-2.5">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            value="1"
                            @checked(old('remember'))
                            class="h-4 w-4 shrink-0 cursor-pointer rounded border-slate-600 bg-slate-950 text-amber-500 focus:ring-2 focus:ring-amber-500/40 focus:ring-offset-0"
                        >
                        <label for="remember" class="cursor-pointer select-none text-sm text-slate-300">
                            Remember me
                        </label>
                    </div>

                    {{-- Submit --}}
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-amber-500 px-4 py-3 font-semibold text-black transition hover:bg-amber-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:ring-offset-2 focus:ring-offset-slate-900"
                    >
                        Log In
                    </button>

                    {{-- Divider --}}
                    <div class="flex items-center gap-4" aria-hidden="true">
                        <span class="h-px flex-1 bg-slate-800"></span>
                        <span class="text-[11px] uppercase tracking-widest text-slate-500">or</span>
                        <span class="h-px flex-1 bg-slate-800"></span>
                    </div>

                    {{-- Google --}}
                    <a
                        href="{{ route('login.google') }}"
                        class="flex w-full items-center justify-center gap-3 rounded-lg border border-slate-700 bg-slate-800/60 px-4 py-3 font-semibold text-slate-100 transition hover:border-slate-600 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500/40"
                    >
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="#4285F4" d="M23.52 12.27c0-.85-.08-1.67-.22-2.45H12v4.64h6.46a5.52 5.52 0 0 1-2.4 3.62v3h3.88c2.27-2.09 3.58-5.17 3.58-8.81z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.94-2.91l-3.88-3.01c-1.08.72-2.45 1.15-4.06 1.15-3.12 0-5.77-2.11-6.71-4.95H1.26v3.1A12 12 0 0 0 12 24z"/>
                            <path fill="#FBBC05" d="M5.29 14.28a7.2 7.2 0 0 1 0-4.56v-3.1H1.26a12 12 0 0 0 0 10.76l4.03-3.1z"/>
                            <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.43-3.43C17.95 1.19 15.24 0 12 0A12 12 0 0 0 1.26 6.62l4.03 3.1C6.23 6.86 8.88 4.75 12 4.75z"/>
                        </svg>
                        Continue with Google
                    </a>
                </form>
            </div>

            <p class="mt-6 text-center text-sm text-slate-400">
                New to VJFlix?
                <a href="{{ route('register') }}" class="font-semibold text-amber-500 transition hover:text-amber-400">
                    Create an account
                </a>
            </p>

            <p class="mt-4 text-center text-xs text-slate-500">
                Free to watch. No subscription required.
            </p>
        </main>
    </section>
</x-layout>
