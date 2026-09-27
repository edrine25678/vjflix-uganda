@props([
    'src',
    'poster' => null,
    'title' => 'Video',
    'vj' => null,
    'type' => 'movie',
    'id' => null,
    'nextUrl' => null,
])

<div 
    x-data="vjflixPlayer({
        src: '{{ $src }}',
        poster: '{{ $poster }}',
        type: '{{ $type }}',
        id: {{ $id ? (int)$id : 'null' }},
        title: '{{ addslashes($title) }}',
        nextUrl: '{{ $nextUrl }}'
    })"
    x-init="initPlayer()"
    @keydown.window="handleKeydown($event)"
    @mousemove="handleActivity()"
    @mouseleave="userActive = false"
    class="relative w-full aspect-video rounded-2xl overflow-hidden bg-black border border-slate-800 shadow-2xl select-none group"
    id="vjflix-player-container">

    <!-- Native Video Element -->
    <video 
        x-ref="video" 
        :poster="poster"
        preload="metadata"
        playsinline
        @timeupdate="onTimeUpdate()"
        @loadedmetadata="onLoadedMetadata()"
        @ended="onEnded()"
        @click="togglePlay()"
        class="w-full h-full object-contain cursor-pointer">
        <source :src="src" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <!-- Top Watermark Bar (Autohides) -->
    <div 
        x-show="userActive || !isPlaying" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="absolute top-0 inset-x-0 p-4 sm:p-6 bg-gradient-to-b from-black/80 via-black/40 to-transparent flex items-center justify-between pointer-events-none z-20">
        
        <div class="flex items-center space-x-3">
            <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-black font-extrabold text-sm font-display shadow">
                VJ
            </span>
            <div>
                <h3 class="text-sm sm:text-base font-bold text-white drop-shadow truncate max-w-xs sm:max-w-md">
                    {{ $title }}
                </h3>
                @if ($vj)
                    <span class="text-[11px] text-amber-400 font-semibold drop-shadow flex items-center gap-1">
                        <x-bi-mic-fill class="h-2.5 w-2.5" />
                        Voiced by {{ $vj }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-2 py-0.5 rounded bg-black/60 border border-slate-700/80 text-[10px] font-bold text-slate-300 backdrop-blur">
                HD 1080p
            </span>
            <span class="px-2 py-0.5 rounded bg-amber-500/20 border border-amber-500/30 text-[10px] font-bold text-amber-400">
                Luganda
            </span>
        </div>
    </div>

    <!-- Center Big Play Button (When Paused) -->
    <div 
        x-show="!isPlaying" 
        @click="togglePlay()"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        class="absolute inset-0 flex items-center justify-center cursor-pointer z-10 bg-black/30 backdrop-blur-[2px]">
        <button type="button" class="flex h-16 w-16 sm:h-20 sm:w-20 items-center justify-center rounded-full bg-amber-500 text-black shadow-2xl hover:scale-110 hover:bg-amber-400 transition-all duration-200">
            <x-bi-play-fill class="h-10 w-10 sm:h-12 sm:w-12 ml-1" />
        </button>
    </div>

    <!-- Resume Playback Toast Notification -->
    <div 
        x-show="showResumePrompt" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-4"
        class="absolute bottom-20 left-6 z-30 flex items-center gap-3 rounded-xl bg-slate-900/95 border border-amber-500/40 p-3 shadow-2xl backdrop-blur text-xs">
        <x-bi-clock-history class="h-4 w-4 text-amber-400 flex-shrink-0" />
        <span class="text-slate-200">
            Resume watching from <strong class="text-amber-400" x-text="resumeTimeFormatted"></strong>?
        </span>
        <button @click="applyResume()" type="button" class="px-2.5 py-1 rounded bg-amber-500 hover:bg-amber-400 text-black font-extrabold text-[11px] shadow">
            Resume
        </button>
        <button @click="dismissResume()" type="button" class="text-slate-400 hover:text-white text-xs">
            ✕
        </button>
    </div>

    <!-- Bottom Controls Overlay -->
    <div 
        x-show="userActive || !isPlaying" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute bottom-0 inset-x-0 p-3 sm:p-5 bg-gradient-to-t from-black/95 via-black/80 to-transparent space-y-2.5 z-20">
        
        <!-- Interactive Progress / Scrub Bar -->
        <div class="relative group/scrub py-1 cursor-pointer" @click="seek($event)">
            <!-- Background Rail -->
            <div class="h-1.5 group-hover/scrub:h-2 w-full rounded-full bg-slate-800 transition-all overflow-hidden relative">
                <!-- Buffer Bar -->
                <div class="h-full bg-slate-700/60 rounded-full" :style="`width: ${bufferedPercentage}%`"></div>
                <!-- Played Bar -->
                <div class="absolute inset-y-0 left-0 bg-amber-500 rounded-full" :style="`width: ${playedPercentage}%`"></div>
            </div>
            <!-- Scrubber Thumb -->
            <div 
                class="absolute top-1/2 -translate-y-1/2 h-3.5 w-3.5 rounded-full bg-amber-400 shadow-md ring-2 ring-black opacity-0 group-hover/scrub:opacity-100 transition-opacity pointer-events-none" 
                :style="`left: calc(${playedPercentage}% - 7px)`"></div>
        </div>

        <!-- Buttons Row -->
        <div class="flex items-center justify-between text-slate-200 text-xs font-semibold">
            <!-- Left Controls: Play, Skip, Volume, Time -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Play/Pause Button -->
                <button @click="togglePlay()" type="button" class="text-white hover:text-amber-400 transition-colors focus:outline-none" title="Play/Pause (Space)">
                    <template x-if="isPlaying">
                        <x-bi-pause-fill class="h-6 w-6" />
                    </template>
                    <template x-if="!isPlaying">
                        <x-bi-play-fill class="h-6 w-6" />
                    </template>
                </button>

                <!-- Rewind 10s -->
                <button @click="skip(-10)" type="button" class="text-slate-300 hover:text-white transition-colors focus:outline-none" title="Rewind 10s (Left Arrow)">
                    <x-bi-arrow-counterclockwise class="h-4 w-4" />
                </button>

                <!-- Forward 10s -->
                <button @click="skip(10)" type="button" class="text-slate-300 hover:text-white transition-colors focus:outline-none" title="Forward 10s (Right Arrow)">
                    <x-bi-arrow-clockwise class="h-4 w-4" />
                </button>

                <!-- Volume & Mute -->
                <div class="flex items-center space-x-1.5 group/vol">
                    <button @click="toggleMute()" type="button" class="text-slate-300 hover:text-white transition-colors focus:outline-none" title="Mute (M)">
                        <template x-if="isMuted || volume === 0">
                            <x-bi-volume-mute-fill class="h-4 w-4 text-red-400" />
                        </template>
                        <template x-if="!isMuted && volume > 0">
                            <x-bi-volume-up-fill class="h-4 w-4" />
                        </template>
                    </button>
                    <input 
                        type="range" 
                        min="0" 
                        max="1" 
                        step="0.05" 
                        x-model="volume" 
                        @input="updateVolume()" 
                        class="w-14 sm:w-20 accent-amber-500 h-1 rounded cursor-pointer hidden group-hover/vol:block transition-all">
                </div>

                <!-- Time Readout -->
                <div class="text-[11px] font-mono text-slate-300">
                    <span x-text="currentTimeFormatted">00:00</span>
                    <span class="text-slate-500">/</span>
                    <span x-text="durationFormatted">00:00</span>
                </div>
            </div>

            <!-- Right Controls: Speed, Next Episode, Fullscreen -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Speed Dropdown -->
                <div x-data="{ speedOpen: false }" class="relative">
                    <button @click="speedOpen = !speedOpen" @click.away="speedOpen = false" type="button" class="px-2 py-0.5 rounded bg-slate-900 border border-slate-700/80 text-[10px] font-bold text-slate-300 hover:text-amber-400">
                        <span x-text="`${playbackRate}x`"></span>
                    </button>
                    <div x-show="speedOpen" x-cloak class="absolute bottom-8 right-0 rounded-xl bg-slate-900 border border-slate-800 p-1 shadow-2xl text-[11px] space-y-0.5 z-30">
                        <template x-for="rate in [0.75, 1.0, 1.25, 1.5, 2.0]" :key="rate">
                            <button 
                                @click="setSpeed(rate); speedOpen = false;" 
                                type="button" 
                                :class="playbackRate === rate ? 'bg-amber-500 text-black font-extrabold' : 'text-slate-300 hover:bg-slate-800'"
                                class="w-full text-left px-3 py-1 rounded-lg">
                                <span x-text="`${rate}x`"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Next Episode CTA (If series) -->
                <template x-if="nextUrl">
                    <a :href="nextUrl" class="hidden sm:inline-flex items-center text-[11px] font-bold text-amber-400 hover:text-amber-300 transition-colors">
                        Next <x-bi-skip-forward-fill class="h-3.5 w-3.5 ml-0.5" />
                    </a>
                </template>

                <!-- Fullscreen Toggle -->
                <button @click="toggleFullscreen()" type="button" class="text-slate-300 hover:text-amber-400 transition-colors focus:outline-none" title="Fullscreen (F)">
                    <x-bi-fullscreen class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function vjflixPlayer(config) {
        return {
            src: config.src,
            poster: config.poster,
            type: config.type,
            id: config.id,
            title: config.title,
            nextUrl: config.nextUrl,
            isPlaying: false,
            isMuted: false,
            volume: 1,
            currentTime: 0,
            duration: 0,
            playedPercentage: 0,
            bufferedPercentage: 0,
            playbackRate: 1.0,
            userActive: true,
            inactivityTimer: null,
            progressInterval: null,
            showResumePrompt: false,
            savedResumeTime: 0,
            resumeTimeFormatted: '00:00',

            initPlayer() {
                const video = this.$refs.video;
                if (!video) return;

                // Check for saved watch progress from database
                if (this.id) {
                    fetch(`/api/progress/${this.type}/${this.id}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.has_progress && data.progress_seconds > 15) {
                                this.savedResumeTime = data.progress_seconds;
                                this.resumeTimeFormatted = data.formatted;
                                this.showResumePrompt = true;

                                // Auto-hide resume prompt after 12 seconds
                                setTimeout(() => {
                                    this.showResumePrompt = false;
                                }, 12000);
                            }
                        })
                        .catch(() => {});
                }

                // Heartbeat to save progress every 10 seconds during playback
                this.progressInterval = setInterval(() => {
                    if (this.isPlaying && this.id) {
                        this.saveProgress();
                    }
                }, 10000);
            },

            handleActivity() {
                this.userActive = true;
                clearTimeout(this.inactivityTimer);
                if (this.isPlaying) {
                    this.inactivityTimer = setTimeout(() => {
                        this.userActive = false;
                    }, 3000);
                }
            },

            togglePlay() {
                const video = this.$refs.video;
                if (video.paused) {
                    video.play();
                    this.isPlaying = true;
                    this.handleActivity();
                } else {
                    video.pause();
                    this.isPlaying = false;
                    this.userActive = true;
                    this.saveProgress();
                }
            },

            skip(seconds) {
                const video = this.$refs.video;
                video.currentTime = Math.max(0, Math.min(video.duration, video.currentTime + seconds));
                this.handleActivity();
            },

            seek(e) {
                const video = this.$refs.video;
                const rect = e.currentTarget.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                video.currentTime = pos * video.duration;
                this.handleActivity();
            },

            toggleMute() {
                const video = this.$refs.video;
                this.isMuted = !this.isMuted;
                video.muted = this.isMuted;
            },

            updateVolume() {
                const video = this.$refs.video;
                video.volume = this.volume;
                this.isMuted = this.volume === 0;
                video.muted = this.isMuted;
            },

            setSpeed(rate) {
                this.$refs.video.playbackRate = rate;
                this.playbackRate = rate;
            },

            toggleFullscreen() {
                const container = document.getElementById('vjflix-player-container');
                if (!document.fullscreenElement) {
                    container.requestFullscreen?.() || container.webkitRequestFullscreen?.();
                } else {
                    document.exitFullscreen?.() || document.webkitExitFullscreen?.();
                }
            },

            onTimeUpdate() {
                const video = this.$refs.video;
                this.currentTime = video.currentTime;
                if (video.duration) {
                    this.playedPercentage = (video.currentTime / video.duration) * 100;
                }
                if (video.buffered.length > 0) {
                    this.bufferedPercentage = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
                }
            },

            onLoadedMetadata() {
                this.duration = this.$refs.video.duration;
            },

            onEnded() {
                this.isPlaying = false;
                this.userActive = true;
                this.saveProgress(true);

                // Auto-advance to next episode if available
                if (this.nextUrl) {
                    setTimeout(() => {
                        window.location.href = this.nextUrl;
                    }, 2500);
                }
            },

            applyResume() {
                const video = this.$refs.video;
                video.currentTime = this.savedResumeTime;
                this.showResumePrompt = false;
                this.togglePlay();
            },

            dismissResume() {
                this.showResumePrompt = false;
            },

            saveProgress(forcedCompleted = false) {
                if (!this.id || this.currentTime <= 0) return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch('/api/progress', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        watchable_type: this.type,
                        watchable_id: this.id,
                        progress_seconds: Math.floor(this.currentTime),
                        duration_seconds: Math.max(1, Math.floor(this.duration || 1))
                    })
                }).catch(() => {});
            },

            handleKeydown(e) {
                // Don't trigger if user is typing in an input
                if (['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) return;

                if (e.code === 'Space' || e.key === 'k') {
                    e.preventDefault();
                    this.togglePlay();
                } else if (e.code === 'ArrowLeft' || e.key === 'j') {
                    e.preventDefault();
                    this.skip(-10);
                } else if (e.code === 'ArrowRight' || e.key === 'l') {
                    e.preventDefault();
                    this.skip(10);
                } else if (e.key === 'f' || e.key === 'F') {
                    e.preventDefault();
                    this.toggleFullscreen();
                } else if (e.key === 'm' || e.key === 'M') {
                    e.preventDefault();
                    this.toggleMute();
                }
            },

            get currentTimeFormatted() {
                return this.formatTime(this.currentTime);
            },

            get durationFormatted() {
                return this.formatTime(this.duration);
            },

            formatTime(seconds) {
                if (!seconds || isNaN(seconds)) return '00:00';
                const s = Math.floor(seconds);
                const hrs = Math.floor(s / 3600);
                const mins = Math.floor((s % 3600) / 60);
                const secs = s % 60;
                if (hrs > 0) {
                    return `${hrs}:${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
                }
                return `${mins < 10 ? '0' : ''}${mins}:${secs < 10 ? '0' : ''}${secs}`;
            }
        };
    }
</script>
