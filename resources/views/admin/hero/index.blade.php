<x-admin-layout>
    <x-slot:title>Hero Carousel Posters Manager — VJFlix Admin</x-slot:title>

    <div class="space-y-8 max-w-7xl mx-auto" x-data="{ addModalOpen: false, editModalOpen: false, editingSlide: null }">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white flex items-center gap-3 font-display tracking-wide">
                    <x-bi-images class="h-8 w-8 text-amber-400" />
                    Hero Carousel Posters
                </h1>
                <p class="text-xs text-slate-400 mt-1">
                    Manage the dynamic top banner posters. Transitions slide smoothly from <strong>left to right</strong> every 3 seconds.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="px-3.5 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-xs font-bold text-slate-300 flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full {{ $slides->count() >= 6 ? 'bg-amber-400' : 'bg-emerald-400 animate-pulse' }}"></span>
                    <span>{{ $slides->count() }} of {{ $maxLimit }} Posters Configured</span>
                </div>

                @if ($remainingSlots > 0)
                    <button @click="addModalOpen = true" 
                            type="button" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg shadow-amber-500/20 transition-all">
                        <x-bi-plus-lg class="h-4 w-4 stroke-[2]" />
                        Add Poster
                    </button>
                @else
                    <button disabled 
                            type="button" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 text-slate-500 text-xs font-bold cursor-not-allowed">
                        Max 6 Posters Reached
                    </button>
                @endif
            </div>
        </div>

        <!-- Info / Tip Banner -->
        <div class="rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-500/30 p-4 text-xs text-slate-300 flex items-start gap-3">
            <x-bi-info-circle-fill class="h-5 w-5 text-amber-400 flex-shrink-0 mt-0.5" />
            <div>
                <strong class="text-amber-400 font-bold">Automatic 3-Second Left-to-Right Slider:</strong>
                The Hero Section on both the landing page and streaming catalog automatically rotates between these posters using a smooth left-to-right slide animation. When no custom posters are active, the system automatically falls back to your top featured movies.
            </div>
        </div>

        <!-- Current Posters Grid (Up to 6) -->
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                    Active Hero Posters ({{ $slides->count() }} / 6)
                </h2>
                <span class="text-xs text-slate-400">Order determines slide sequence (1 to 6)</span>
            </div>

            @if ($slides->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($slides as $slide)
                        <div class="group relative rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl flex flex-col justify-between hover:border-amber-500/50 transition-all">
                            <!-- Slide Image Preview -->
                            <div class="relative aspect-video w-full overflow-hidden bg-slate-950">
                                <img src="{{ $slide->backdropUrl() }}" alt="{{ $slide->title }}" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/40 to-transparent"></div>

                                <!-- Position Badge -->
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-amber-500 text-black text-xs font-black shadow-lg">
                                    Slide #{{ $slide->sort_order }}
                                </span>

                                <!-- Status Badge -->
                                <span class="absolute top-3 right-3 px-2 py-0.5 rounded text-[10px] font-bold {{ $slide->is_active ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                                    {{ $slide->is_active ? 'Active' : 'Hidden' }}
                                </span>

                                <div class="absolute bottom-2.5 left-3 right-3">
                                    <h3 class="font-bold text-sm text-white truncate drop-shadow">{{ $slide->title }}</h3>
                                    <div class="flex items-center gap-2 text-[11px] text-amber-400 font-semibold drop-shadow mt-0.5">
                                        <span>Voiced by {{ $slide->resolved_vj }}</span>
                                        <span>•</span>
                                        <span>{{ $slide->resolved_year }}</span>
                                        <span>•</span>
                                        <span>★ {{ $slide->resolved_rating }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Details & Synopsis -->
                            <div class="p-4 flex-grow flex flex-col justify-between space-y-4">
                                <p class="text-xs text-slate-400 line-clamp-2">
                                    {{ $slide->resolved_synopsis }}
                                </p>

                                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs">
                                    <!-- Edit Trigger -->
                                    <button 
                                        @click="editingSlide = {{ json_encode($slide) }}; editModalOpen = true"
                                        type="button" 
                                        class="inline-flex items-center gap-1.5 text-amber-400 hover:text-amber-300 font-bold transition-colors">
                                        <x-bi-pencil class="h-3.5 w-3.5" />
                                        Edit Slide
                                    </button>

                                    <!-- Delete Button -->
                                    <form action="{{ route('admin.hero.destroy', $slide->id) }}" method="POST" onsubmit="return confirm('Remove this poster from the Hero Carousel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 text-red-400 hover:text-red-300 text-xs font-semibold transition-colors">
                                            <x-bi-trash class="h-3.5 w-3.5" />
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-2xl border border-dashed border-slate-800 p-10 text-center text-slate-400">
                    <x-bi-images class="mx-auto h-12 w-12 text-slate-600 mb-3" />
                    <h3 class="text-sm font-bold text-slate-200">No Custom Hero Posters Created Yet</h3>
                    <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">
                        The public site is currently displaying the 6 top published featured movies by default. You can customize up to 6 posters below.
                    </p>
                </div>
            @endif
        </div>

        <!-- Quick Feature From Catalog (If slots remain) -->
        @if ($remainingSlots > 0 && $availableMovies->isNotEmpty())
            <div class="pt-6 border-t border-slate-800">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-1.5 h-4 rounded-full bg-amber-500 inline-block"></span>
                        Quick-Add from Movie Catalog ({{ $remainingSlots }} Slots Free)
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Click any movie below to instantly place it into the Hero Carousel.</p>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                    @foreach ($availableMovies->take(6) as $aMovie)
                        <div class="rounded-xl bg-slate-900 border border-slate-800 p-2 flex flex-col justify-between">
                            <div class="aspect-video w-full rounded-lg overflow-hidden bg-slate-800 mb-2">
                                <img src="{{ $aMovie->backdropUrl() }}" alt="{{ $aMovie->title }}" class="h-full w-full object-cover">
                            </div>
                            <h4 class="font-bold text-xs text-white truncate" title="{{ $aMovie->title }}">{{ $aMovie->title }}</h4>
                            <p class="text-[10px] text-amber-400 truncate">{{ $aMovie->vj ? $aMovie->vj->stage_name : 'Luganda' }}</p>
                            
                            <form action="{{ route('admin.hero.quick-add', $aMovie->id) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="submit" class="w-full py-1.5 rounded-lg bg-amber-500/20 hover:bg-amber-500 hover:text-black border border-amber-500/30 text-[10px] font-bold text-amber-300 transition-colors text-center">
                                    + Add to Hero
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Add Poster Modal -->
        <div x-cloak x-show="addModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="addModalOpen = false" class="relative w-full max-w-2xl rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl text-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-bi-plus-circle-fill class="h-5 w-5 text-amber-400" />
                        Add Hero Poster (Max 6)
                    </h3>
                    <button @click="addModalOpen = false" type="button" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <form action="{{ route('admin.hero.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <!-- Select from Existing Movie -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Pre-fill From Movie (Optional)</label>
                        <select 
                            @change="
                                const opt = $event.target.options[$event.target.selectedIndex];
                                if (opt.value) {
                                    $refs.titleInput.value = opt.dataset.title;
                                    $refs.vjInput.value = opt.dataset.vj;
                                    $refs.yearInput.value = opt.dataset.year;
                                    $refs.ratingInput.value = opt.dataset.rating;
                                    $refs.backdropInput.value = opt.dataset.backdrop;
                                    $refs.synopsisInput.value = opt.dataset.synopsis;
                                    $refs.watchInput.value = opt.dataset.watch;
                                    $refs.downloadInput.value = opt.dataset.download;
                                }
                            "
                            name="movie_id" 
                            class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white focus:border-amber-500">
                            <option value="">-- Choose a movie to pre-fill, or enter custom details below --</option>
                            @foreach ($availableMovies as $mov)
                                <option 
                                    value="{{ $mov->id }}"
                                    data-title="{{ $mov->title }}"
                                    data-vj="{{ $mov->vj ? $mov->vj->stage_name : 'Luganda' }}"
                                    data-year="{{ $mov->release_year }}"
                                    data-rating="{{ number_format($mov->average_rating, 1) }}"
                                    data-backdrop="{{ $mov->backdropUrl() }}"
                                    data-synopsis="{{ $mov->synopsis ?: $mov->description }}"
                                    data-watch="{{ route('movies.show', $mov->slug) }}"
                                    data-download="{{ route('movies.download', $mov->slug) }}"
                                >
                                    {{ $mov->title }} ({{ $mov->release_year }}) — {{ $mov->vj ? $mov->vj->stage_name : 'Luganda' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Poster Title *</label>
                            <input x-ref="titleInput" type="text" name="title" required class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="e.g. Avatar: The Way of Water">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">VJ Voice Actor</label>
                            <input x-ref="vjInput" type="text" name="vj_name" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="e.g. VJ Junior">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Release Year</label>
                            <input x-ref="yearInput" type="text" name="release_year" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="2024">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Rating</label>
                            <input x-ref="ratingInput" type="text" name="rating" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="4.9">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Slide Order (1-6)</label>
                            <input type="number" min="1" max="6" name="sort_order" value="{{ $slides->count() + 1 }}" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Backdrop Image URL (1920x1080 recommended)</label>
                        <input x-ref="backdropInput" type="url" name="backdrop_url" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="https://image.tmdb.org/t/p/original/... or image URL">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Or Upload Custom Backdrop File</label>
                        <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Synopsis / Storyline</label>
                        <textarea x-ref="synopsisInput" name="synopsis" rows="2" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="Brief movie description shown on the hero banner..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Watch / Stream URL</label>
                            <input x-ref="watchInput" type="text" name="watch_url" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="/movie/slug or URL">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Direct Download URL</label>
                            <input x-ref="downloadInput" type="text" name="download_url" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white" placeholder="/download/movie/slug or URL">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" name="is_active" id="add_active" value="1" checked class="h-4 w-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500">
                        <label for="add_active" class="text-xs font-semibold text-slate-300">Set active in Hero Carousel</label>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
                        <button @click="addModalOpen = false" type="button" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg">Save Hero Poster</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Poster Modal -->
        <div x-cloak x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="editModalOpen = false" class="relative w-full max-w-2xl rounded-2xl bg-slate-900 border border-slate-800 p-6 shadow-2xl text-slate-100">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <x-bi-pencil-square class="h-5 w-5 text-amber-400" />
                        Edit Hero Poster
                    </h3>
                    <button @click="editModalOpen = false" type="button" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <form :action="`/admin/hero/${editingSlide ? editingSlide.id : ''}`" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Poster Title *</label>
                            <input type="text" name="title" :value="editingSlide ? editingSlide.title : ''" required class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">VJ Voice Actor</label>
                            <input type="text" name="vj_name" :value="editingSlide ? editingSlide.vj_name : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Release Year</label>
                            <input type="text" name="release_year" :value="editingSlide ? editingSlide.release_year : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Rating</label>
                            <input type="text" name="rating" :value="editingSlide ? editingSlide.rating : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Slide Order (1-6)</label>
                            <input type="number" min="1" max="6" name="sort_order" :value="editingSlide ? editingSlide.sort_order : 1" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Backdrop Image URL</label>
                        <input type="url" name="backdrop_url" :value="editingSlide ? editingSlide.backdrop_url : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Replace Backdrop File</label>
                        <input type="file" name="backdrop_file" accept="image/*" class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 mb-1">Synopsis</label>
                        <textarea name="synopsis" rows="2" :value="editingSlide ? editingSlide.synopsis : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Watch URL</label>
                            <input type="text" name="watch_url" :value="editingSlide ? editingSlide.watch_url : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Download URL</label>
                            <input type="text" name="download_url" :value="editingSlide ? editingSlide.download_url : ''" class="w-full rounded-xl bg-slate-800 border border-slate-700 px-3 py-2 text-xs text-white">
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" name="is_active" id="edit_active" value="1" :checked="editingSlide && editingSlide.is_active" class="h-4 w-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500">
                        <label for="edit_active" class="text-xs font-semibold text-slate-300">Active in Hero Carousel</label>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex justify-end gap-3">
                        <button @click="editModalOpen = false" type="button" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Cancel</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-black text-xs font-extrabold shadow-lg">Update Poster</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
