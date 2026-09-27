@props([
    'item' => null,
    'type' => 'movie', // 'movie' or 'series'
])

@php
    $reviews = $item->reviews()->with('user')->get();
    $avgRating = $item->average_rating ?? ($reviews->avg('rating') ?: 0);
    $userReview = auth()->check() ? $reviews->firstWhere('user_id', auth()->id()) : null;
@endphp

<div id="reviews-section" class="mt-12 pt-8 border-t border-neutral-800">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="text-2xl font-bold text-white flex items-center gap-3">
                <span>Audience Reviews & VJ Rating</span>
                <span class="text-sm font-normal px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ $reviews->count() }} {{ Str::plural('review', $reviews->count()) }}
                </span>
            </h3>
            <p class="text-sm text-neutral-400 mt-1">Share your thoughts on the movie translation and commentary quality</p>
        </div>

        @if($avgRating > 0)
            <div class="flex items-center gap-3 bg-neutral-900 border border-neutral-800 px-4 py-2.5 rounded-xl">
                <div class="text-right">
                    <div class="text-2xl font-black text-amber-400 leading-none">{{ number_format($avgRating, 1) }}</div>
                    <div class="text-[10px] uppercase font-bold text-neutral-500 tracking-wider">Out of 5</div>
                </div>
                <x-rating-stars :rating="$avgRating" size="lg" :showScore="false" />
            </div>
        @endif
    </div>

    {{-- Review Form for Authenticated Users --}}
    @auth
        <div x-data="{
            rating: {{ $userReview ? $userReview->rating : 5 }},
            tempRating: 0,
            submitting: false
        }" class="bg-neutral-900/60 border border-neutral-800 rounded-2xl p-6 mb-10 shadow-lg">
            <h4 class="text-lg font-semibold text-white mb-2">
                {{ $userReview ? 'Update Your Review' : 'Rate this Translation' }}
            </h4>
            <p class="text-xs text-neutral-400 mb-4">
                How well did the VJ interpret this {{ $type }}? Tap a star to rate.
            </p>

            <form action="{{ route('reviews.store') }}" method="POST" @submit="submitting = true">
                @csrf
                <input type="hidden" name="reviewable_type" value="{{ $type }}">
                <input type="hidden" name="reviewable_id" value="{{ $item->id }}">
                <input type="hidden" name="rating" :value="rating">

                {{-- Interactive Star Selector --}}
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xs font-semibold text-neutral-300 uppercase tracking-wider">Your Rating:</span>
                    <div class="flex items-center gap-1 cursor-pointer">
                        <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                            <button 
                                type="button"
                                @click="rating = star"
                                @mouseenter="tempRating = star"
                                @mouseleave="tempRating = 0"
                                class="p-1 text-2xl focus:outline-none transition-transform hover:scale-125"
                                :class="(tempRating || rating) >= star ? 'text-amber-400' : 'text-neutral-600'"
                            >
                                ★
                            </button>
                        </template>
                    </div>
                    <span class="text-sm font-bold text-amber-400 ml-2" x-text="rating + ' / 5 Stars'"></span>
                </div>

                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <input 
                            type="text" 
                            name="title" 
                            placeholder="Title of your review (e.g. 'VJ Junior nailed the humor!')" 
                            value="{{ old('title', $userReview?->title) }}"
                            class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >
                    </div>
                    <div>
                        <textarea 
                            name="body" 
                            rows="3" 
                            placeholder="Write your review... Mention the translation fidelity, pacing, sound clarity, and overall vibe."
                            class="w-full bg-neutral-800/80 border border-neutral-700 rounded-xl p-4 text-sm text-white placeholder-neutral-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500"
                        >{{ old('body', $userReview?->body) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        :disabled="submitting"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-neutral-950 shadow-md transition active:scale-95 disabled:opacity-50"
                    >
                        <span x-text="submitting ? 'Submitting...' : '{{ $userReview ? 'Update Review' : 'Post Review' }}'"></span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-neutral-900/60 border border-neutral-800 rounded-2xl p-6 mb-10 text-center">
            <p class="text-neutral-300 text-sm mb-3">Sign in to rate this title and share your thoughts with the VJ community.</p>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-semibold bg-amber-500 text-neutral-950 hover:bg-amber-400 transition">
                Sign In to Review
            </a>
        </div>
    @endauth

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="space-y-4">
            @foreach($reviews as $rev)
                <div class="bg-neutral-900/40 border border-neutral-800/80 rounded-2xl p-5 hover:border-neutral-700 transition">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <img src="{{ $rev->user->avatarUrl() }}" alt="{{ $rev->user->name }}" class="w-10 h-10 rounded-full border border-neutral-700 object-cover">
                            <div>
                                <div class="font-semibold text-sm text-white flex items-center gap-2">
                                    <span>{{ $rev->user->name }}</span>
                                    @if($rev->user->preferredVj)
                                        <span class="text-[11px] font-normal text-neutral-400 bg-neutral-800 px-2 py-0.5 rounded-md">
                                            Fan of {{ $rev->user->preferredVj->stage_name }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-neutral-400">{{ $rev->created_at->diffForHumans() }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-rating-stars :rating="$rev->rating" size="md" :showScore="true" />
                            @if(auth()->check() && (auth()->id() === $rev->user_id || auth()->user()->isAdmin()))
                                <form action="{{ route('reviews.destroy', $rev) }}" method="POST" onsubmit="return confirm('Delete this review?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-neutral-500 hover:text-red-400 p-1 transition" title="Delete review">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($rev->title)
                        <h5 class="font-bold text-sm text-white mb-1.5">{{ $rev->title }}</h5>
                    @endif

                    @if($rev->body)
                        <p class="text-sm text-neutral-300 leading-relaxed">{{ $rev->body }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-10 bg-neutral-900/20 border border-dashed border-neutral-800 rounded-2xl">
            <div class="text-3xl mb-2">⭐</div>
            <p class="text-neutral-400 text-sm">No reviews yet for this title.</p>
            <p class="text-neutral-400 text-xs mt-1">Be the first to rate and review this Luganda translation!</p>
        </div>
    @endif
</div>
