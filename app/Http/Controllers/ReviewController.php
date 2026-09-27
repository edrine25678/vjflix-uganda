<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Submit or update a user rating and review.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reviewable_type' => ['required', 'string', 'in:movie,series'],
            'reviewable_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:150'],
            'body' => ['nullable', 'string', 'max:2000'],
        ]);

        $modelClass = $validated['reviewable_type'] === 'movie'
            ? Movie::class
            : Series::class;

        $item = $modelClass::findOrFail($validated['reviewable_id']);

        $review = Review::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'reviewable_type' => $modelClass,
                'reviewable_id' => $item->id,
            ],
            [
                'rating' => $validated['rating'],
                'title' => $validated['title'] ?? null,
                'body' => $validated['body'] ?? null,
                'is_approved' => true,
            ]
        );

        $item->recalculateRating();

        $message = "Your {$validated['rating']}-star review for '{$item->title}' has been posted!";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'review' => $review->load('user'),
                'average_rating' => $item->fresh()->average_rating,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review)
    {
        if (auth()->id() !== $review->user_id && ! auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $reviewable = $review->reviewable;
        $review->delete();

        if ($reviewable && method_exists($reviewable, 'recalculateRating')) {
            $reviewable->recalculateRating();
        }

        $message = 'Review deleted successfully.';

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['status' => 'success', 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
