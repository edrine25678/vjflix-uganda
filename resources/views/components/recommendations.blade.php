@props(['type' => 'personalized', 'title' => 'Recommended For You', 'limit' => 10])

@php
    $recommendations = [];
    $user = auth()->user();

    if ($user) {
        $endpoint = match($type) {
            'personalized' => route('recommendations.personalized', ['limit' => $limit]),
            'watch-history' => route('recommendations.watch-history', ['limit' => $limit]),
            'trending' => route('recommendations.trending', ['limit' => $limit]),
            'new' => route('recommendations.new', ['limit' => $limit]),
            default => route('recommendations.trending', ['limit' => $limit]),
        };
    }
@endphp

<section class="my-8" x-data="{ recommendations: [], loading: true }" x-init="loadRecommendations()">
    <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg md:text-xl font-extrabold tracking-wide text-white flex items-center gap-2">
            <span class="w-1.5 h-5 rounded-full bg-purple-500 inline-block"></span>
            {{ $title }}
        </h2>
    </div>

    <div class="flex overflow-x-auto pb-4 pt-1 no-scrollbar scroll-smooth">
        <template x-if="loading">
            <div class="flex gap-4">
                @for($i = 0; $i < 5; $i++)
                    <div class="flex-shrink-0 w-32 md:w-40 lg:w-48 h-48 md:h-56 lg:h-64 bg-gray-800 rounded-lg animate-pulse"></div>
                @endfor
            </div>
        </template>

        <template x-if="!loading && recommendations.length > 0">
            <div class="flex gap-4">
                <template x-for="item in recommendations" :key="item.id">
                    <a :href="item.type === 'movie' ? '/movie/' + item.slug : '/series/' + item.slug" 
                       class="flex-shrink-0 w-32 md:w-40 lg:w-48 group cursor-pointer">
                        <div class="relative rounded-lg overflow-hidden bg-gray-800 aspect-[2/3]">
                            <img :src="item.poster" :alt="item.title" 
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-0 left-0 right-0 p-3">
                                    <div class="text-white font-semibold text-sm truncate" x-text="item.title"></div>
                                    <div class="text-gray-300 text-xs mt-1" x-text="item.year"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="text-white font-medium text-sm truncate" x-text="item.title"></div>
                            <div class="text-gray-400 text-xs flex items-center gap-2">
                                <span x-text="item.year"></span>
                                <span x-if="item.rating" class="flex items-center gap-1">
                                    <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                    <span x-text="item.rating"></span>
                                </span>
                            </div>
                            <div x-if="item.vj" class="text-purple-400 text-xs mt-1" x-text="'Translated by ' + item.vj"></div>
                        </div>
                    </a>
                </template>
            </div>
        </template>

        <template x-if="!loading && recommendations.length === 0">
            <div class="text-xs text-slate-500 italic py-4">No recommendations available yet.</div>
        </template>
    </div>
</section>

@push('scripts')
<script>
function loadRecommendations() {
    @if($user)
        fetch('{{ $endpoint }}')
            .then(response => response.json())
            .then(data => {
                this.recommendations = data.data || [];
                this.loading = false;
            })
            .catch(error => {
                console.error('Error loading recommendations:', error);
                this.loading = false;
            });
    @else
        this.loading = false;
    @endif
}
</script>
@endpush
