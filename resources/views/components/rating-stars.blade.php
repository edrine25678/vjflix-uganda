@props([
    'rating' => 0,
    'max' => 5,
    'size' => 'md', // 'sm', 'md', 'lg'
    'showScore' => true,
    'reviewsCount' => null,
])

@php
    $starSize = match($size) {
        'sm' => 'w-3.5 h-3.5',
        'lg' => 'w-5 h-5',
        default => 'w-4 h-4',
    };
    $textSize = match($size) {
        'sm' => 'text-xs',
        'lg' => 'text-sm font-semibold',
        default => 'text-xs font-medium',
    };
    $score = round((float) $rating, 1);
@endphp

<div class="inline-flex items-center gap-1.5">
    <div class="flex items-center text-amber-400">
        @for ($i = 1; $i <= $max; $i++)
            @if ($i <= floor($score))
                {{-- Full Star --}}
                <svg class="{{ $starSize }} fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @elseif ($i - $score < 1 && $i - $score > 0)
                {{-- Half Star (Approx) --}}
                <svg class="{{ $starSize }} text-amber-400 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" opacity="0.6"/>
                </svg>
            @else
                {{-- Empty Star --}}
                <svg class="{{ $starSize }} text-neutral-600 fill-current" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endif
        @endfor
    </div>
    @if ($showScore && $score > 0)
        <span class="{{ $textSize }} text-amber-400 font-semibold">{{ number_format($score, 1) }}</span>
    @endif
    @if ($reviewsCount !== null && $reviewsCount > 0)
        <span class="{{ $textSize }} text-neutral-400">({{ $reviewsCount }})</span>
    @endif
</div>
