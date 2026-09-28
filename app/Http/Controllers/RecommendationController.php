<?php

namespace App\Http\Controllers;

use App\Services\ContentBasedRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected ContentBasedRecommendationService $recommendationService;

    public function __construct(ContentBasedRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get personalized recommendations for the authenticated user.
     */
    public function personalized(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getPersonalizedRecommendations($user, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get recommendations based on watch history.
     */
    public function basedOnWatchHistory(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getBasedOnWatchHistory($user, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get similar movies to a specific movie.
     */
    public function similar(Request $request, $id)
    {
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getSimilarMovies((int) $id, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get more content from a specific VJ.
     */
    public function moreFromVj(Request $request, $id)
    {
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getMoreFromVj((int) $id, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get trending content.
     */
    public function trending(Request $request)
    {
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getTrending($limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get new releases.
     */
    public function newReleases(Request $request)
    {
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getNewReleases($limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }

    /**
     * Get recommendations based on genre preferences.
     */
    public function basedOnGenres(Request $request)
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $recommendations = $this->recommendationService->getBasedOnGenres($user, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations,
        ]);
    }
}
