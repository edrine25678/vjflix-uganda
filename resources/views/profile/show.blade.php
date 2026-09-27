<x-layout>
    <x-slot:title>Profile & Preferences — VJFlix Uganda</x-slot:title>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Flash Notification --}}
        @if (session('success'))
            <div class="mb-8 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-sm flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Hero Card --}}
        <div class="bg-gradient-to-r from-neutral-900 via-neutral-900/90 to-amber-950/20 border border-neutral-800 rounded-3xl p-6 sm:p-8 mb-8 shadow-2xl relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <img 
                    src="{{ $user->avatarUrl() }}" 
                    alt="{{ $user->name }}" 
                    class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover border-2 border-amber-500/40 shadow-xl"
                >
                <div class="text-center sm:text-left flex-1">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-2">
                        <h1 class="text-2xl sm:text-3xl font-black text-white">{{ $user->name }}</h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            {{ $user->role ?? 'Viewer' }}
                        </span>
                    </div>
                    <p class="text-neutral-400 text-sm mb-4">{{ $user->email }} • Joined {{ $user->created_at->format('M Y') }}</p>
                    
                    @if($user->preferredVj)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-xl bg-neutral-800/80 border border-neutral-700 text-xs text-neutral-300">
                            <span class="text-amber-400 font-bold">Favorite VJ:</span>
                            <span>{{ $user->preferredVj->stage_name }}</span>
                        </div>
                    @endif
                </div>

                {{-- Activity Counters --}}
                <div class="grid grid-cols-3 gap-3 w-full sm:w-auto">
                    <div class="bg-neutral-950/60 border border-neutral-800/80 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-amber-400">{{ $stats['watchlist_count'] }}</div>
                        <div class="text-[11px] font-semibold text-neutral-400 uppercase mt-0.5">My List</div>
                    </div>
                    <div class="bg-neutral-950/60 border border-neutral-800/80 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-amber-400">{{ $stats['reviews_count'] }}</div>
                        <div class="text-[11px] font-semibold text-neutral-400 uppercase mt-0.5">Reviews</div>
                    </div>
                    <div class="bg-neutral-950/60 border border-neutral-800/80 rounded-2xl p-4 text-center">
                        <div class="text-2xl font-black text-amber-400">{{ $stats['watched_count'] }}</div>
                        <div class="text-[11px] font-semibold text-neutral-400 uppercase mt-0.5">Watched</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Main Preferences Form --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-neutral-900 border border-neutral-800 rounded-3xl p-6 sm:p-8 shadow-xl">
                    <h2 class="text-xl font-bold text-white mb-2">Personal Details & Ugandan Cinema Preferences</h2>
                    <p class="text-sm text-neutral-400 mb-6">Customize your profile and default translation choices.</p>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Full Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                value="{{ old('name', $user->name) }}" 
                                required
                                class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Email Address</label>
                            <input 
                                type="email" 
                                name="email" 
                                value="{{ old('email', $user->email) }}" 
                                required
                                class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Favorite Video Jockey (VJ)</label>
                                <select 
                                    name="preferred_vj_id" 
                                    class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                >
                                    <option value="">-- No preference / All VJs --</option>
                                    @foreach($vjs as $vj)
                                        <option value="{{ $vj->id }}" {{ old('preferred_vj_id', $user->preferred_vj_id) == $vj->id ? 'selected' : '' }}>
                                            {{ $vj->stage_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-[11px] text-neutral-500 mt-1">We'll prioritize recommendations translated by your chosen VJ.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Primary Dialect / Language</label>
                                <select 
                                    name="preferred_language" 
                                    class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                                >
                                    @foreach(['Luganda', 'English', 'Swahili', 'Runyankole', 'Lusoga'] as $lang)
                                        <option value="{{ $lang }}" {{ old('preferred_language', $user->preferred_language) === $lang ? 'selected' : '' }}>
                                            {{ $lang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Upload Profile Photo</label>
                            <input 
                                type="file" 
                                name="profile_photo" 
                                accept="image/*"
                                class="w-full text-sm text-neutral-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-amber-500 file:text-neutral-950 hover:file:bg-amber-400 file:cursor-pointer bg-neutral-800/50 rounded-xl p-2 border border-neutral-700"
                            >
                        </div>

                        <div class="flex justify-end pt-2">
                            <button 
                                type="submit" 
                                class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold text-sm shadow-md transition active:scale-95"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security & Password Form --}}
            <div class="space-y-6">
                <div class="bg-neutral-900 border border-neutral-800 rounded-3xl p-6 sm:p-8 shadow-xl">
                    <h2 class="text-xl font-bold text-white mb-2">Security & Password</h2>
                    <p class="text-sm text-neutral-400 mb-6">Update your account password.</p>

                    <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Current Password</label>
                            <input 
                                type="password" 
                                name="current_password" 
                                required
                                class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">New Password</label>
                            <input 
                                type="password" 
                                name="password" 
                                required
                                class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-300 mb-2">Confirm New Password</label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                required
                                class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-2.5 text-white text-sm focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                            >
                        </div>

                        <div class="pt-2">
                            <button 
                                type="submit" 
                                class="w-full py-2.5 rounded-xl bg-neutral-800 hover:bg-neutral-700 text-white font-semibold text-sm border border-neutral-700 transition"
                            >
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>
