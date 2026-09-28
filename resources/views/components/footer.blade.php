<footer class="mt-20 border-t border-slate-800/80 bg-slate-950 py-12 text-xs text-slate-400">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 mb-8">
            <!-- Brand Column -->
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center space-x-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-base font-display">
                        VJ
                    </span>
                    <span class="text-xl font-extrabold text-white tracking-wider font-display">
                        FLIX <span class="text-amber-400">Uganda</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 max-w-sm leading-relaxed">
                    "Your Movies. Your VJs. Your Language." — Uganda's premier streaming platform dedicated to celebrating Video Jockey translations and local cinema culture.
                </p>
                <div class="pt-2 flex items-center space-x-2 text-[11px] text-slate-500">
                    <span>Supported Payments:</span>
                    <span class="px-2 py-0.5 rounded bg-yellow-500/10 text-yellow-400 font-bold border border-yellow-500/20">MTN MoMo</span>
                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-400 font-bold border border-red-500/20">Airtel Money</span>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-bold text-slate-200 text-xs uppercase tracking-wider mb-3">Explore</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="{{ route('vjflix.index') }}" class="hover:text-amber-400 transition-colors">Movies Catalog</a></li>
                    <li><a href="/vjs" class="hover:text-amber-400 transition-colors">Ugandan VJs</a></li>
                    <li><a href="/series" class="hover:text-amber-400 transition-colors">Translated Series</a></li>
                    <li><a href="{{ route('vjflix.index') }}" class="hover:text-amber-400 transition-colors">Trending in Kampala</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div>
                <h4 class="font-bold text-slate-200 text-xs uppercase tracking-wider mb-3">Popular VJs</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="/vjs/vj-junior" class="hover:text-amber-400 transition-colors">VJ Junior</a></li>
                    <li><a href="/vjs/vj-jingo" class="hover:text-amber-400 transition-colors">VJ Jingo</a></li>
                    <li><a href="/vjs/vj-emmy" class="hover:text-amber-400 transition-colors">VJ Emmy</a></li>
                    <li><a href="/vjs/vj-ice-p" class="hover:text-amber-400 transition-colors">VJ Ice P</a></li>
                </ul>
            </div>

            <!-- Legal / Account -->
            <div>
                <h4 class="font-bold text-slate-200 text-xs uppercase tracking-wider mb-3">Platform</h4>
                <ul class="space-y-2 text-slate-400">
                    <li><a href="/dashboard" class="hover:text-amber-400 transition-colors">My Library</a></li>
                    <li><a href="#" class="hover:text-amber-400 transition-colors">Subscription Plans</a></li>
                    <li><a href="#" class="hover:text-amber-400 transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-amber-400 transition-colors">Privacy Policy</a></li>
                </ul>
            </div>
        </div>

        <div class="pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-[11px] text-slate-500 gap-4">
            <p>&copy; {{ date('Y') }} VJFlix Uganda. All rights reserved. Handcrafted for Ugandan film lovers.</p>
            <p>Kampala, Uganda • Timezone: Africa/Kampala (EAT)</p>
        </div>
    </div>
</footer>
