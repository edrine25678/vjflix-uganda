<?php

namespace App\Services;

use App\Models\User;

interface RecommendationServiceInterface
{
    /**
     * Get personalized recommendations for a user.
     */
    public function getPersonalizedRecommendations(User $user, int $limit = 10): array;

    /**
     * Get recommendations based on watch history.
     */
    public function getBasedOnWatchHistory(User $user, int $limit = 10): array;

    /**
     * Get recommendations based on a specific movie.
     */
    public function getSimilarMovies(int $movieId, int $limit = 10): array;

    /**
     * Get recommendations based on a specific VJ.
     */
    public function getMoreFromVj(int $vjId, int $limit = 10): array;

    /**
     * Get trending recommendations.
     */
    public function getTrending(int $limit = 10): array;

    /**
     * Get new releases.
     */
    public function getNewReleases(int $limit = 10): array;

    /**
     * Get recommendations based on genre preferences.
     */
    public function getBasedOnGenres(User $user, int $limit = 10): array;
}
