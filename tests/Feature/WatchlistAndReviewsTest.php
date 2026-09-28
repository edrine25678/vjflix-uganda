<?php

use App\Models\Movie;
use App\Models\Review;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use App\Models\Watchlist;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can toggle movie in watchlist', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id, 'title' => 'John Wick 4 (Luganda)']);

    // Add to watchlist
    $response = $this->actingAs($user)
        ->postJson('/watchlist/toggle', [
            'watchable_type' => 'movie',
            'watchable_id' => $movie->id,
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'in_watchlist' => true,
    ]);

    $this->assertDatabaseHas('watchlists', [
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
    ]);

    // Remove from watchlist
    $response2 = $this->actingAs($user)
        ->postJson('/watchlist/toggle', [
            'watchable_type' => 'movie',
            'watchable_id' => $movie->id,
        ]);

    $response2->assertStatus(200);
    $response2->assertJson([
        'status' => 'success',
        'in_watchlist' => false,
    ]);

    $this->assertDatabaseMissing('watchlists', [
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
    ]);
});

test('authenticated user can toggle series in watchlist', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $series = Series::factory()->create(['vj_id' => $vj->id, 'title' => 'Breaking Bad (Luganda)']);

    $response = $this->actingAs($user)
        ->postJson('/watchlist/toggle', [
            'watchable_type' => 'series',
            'watchable_id' => $series->id,
        ]);

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'in_watchlist' => true,
    ]);

    $this->assertDatabaseHas('watchlists', [
        'user_id' => $user->id,
        'watchable_type' => Series::class,
        'watchable_id' => $series->id,
    ]);
});

test('unauthenticated user cannot toggle watchlist', function () {
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    $response = $this
        ->postJson('/watchlist/toggle', [
            'watchable_type' => 'movie',
            'watchable_id' => $movie->id,
        ]);

    $response->assertStatus(401);
});

test('user can view populated my-list page', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create(['stage_name' => 'VJ Junior']);

    $movie = Movie::factory()->create([
        'title' => 'Fast X (Luganda)',
        'vj_id' => $vj->id,
    ]);

    $series = Series::factory()->create([
        'title' => 'Lupin (Luganda)',
        'vj_id' => $vj->id,
    ]);

    Watchlist::create([
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
    ]);

    Watchlist::create([
        'user_id' => $user->id,
        'watchable_type' => Series::class,
        'watchable_id' => $series->id,
    ]);

    $response = $this->actingAs($user)->get('/my-list');

    $response->assertStatus(200);
    $response->assertSee('Fast X (Luganda)');
    $response->assertSee('Lupin (Luganda)');
    $response->assertSee('VJ Junior');
});

test('user can submit review and recalculate movie average rating', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create([
        'vj_id' => $vj->id,
        'average_rating' => 0,
        'ratings_count' => 0,
    ]);

    $response = $this->actingAs($user)
        ->post('/reviews', [
            'reviewable_type' => 'movie',
            'reviewable_id' => $movie->id,
            'rating' => 5,
            'title' => 'Top notch commentary',
            'body' => 'The translations in this action movie were hilarious and accurate.',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'reviewable_type' => Movie::class,
        'reviewable_id' => $movie->id,
        'rating' => 5,
        'title' => 'Top notch commentary',
    ]);

    $movie->refresh();
    expect((float) $movie->average_rating)->toBe(5.0);
    expect($movie->ratings_count)->toBe(1);
});

test('user can update their previous review', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    Review::create([
        'user_id' => $user->id,
        'reviewable_type' => Movie::class,
        'reviewable_id' => $movie->id,
        'rating' => 5,
        'title' => 'Initial review',
    ]);

    $response = $this->actingAs($user)
        ->postJson('/reviews', [
            'reviewable_type' => 'movie',
            'reviewable_id' => $movie->id,
            'rating' => 4,
            'title' => 'Revised title',
            'body' => 'Still great, but sound was slightly low in part 2.',
        ]);

    $response->assertStatus(200);

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'reviewable_id' => $movie->id,
        'rating' => 4,
        'title' => 'Revised title',
    ]);

    $movie->refresh();
    expect((float) $movie->average_rating)->toBe(4.0);
});

test('user can delete their review', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    $review = Review::create([
        'user_id' => $user->id,
        'reviewable_type' => Movie::class,
        'reviewable_id' => $movie->id,
        'rating' => 5,
    ]);

    $movie->recalculateRating();
    expect((float) $movie->fresh()->average_rating)->toBe(5.0);

    $response = $this->actingAs($user)
        ->delete("/reviews/{$review->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('reviews', ['id' => $review->id]);

    expect((float) $movie->fresh()->average_rating)->toBe(0.0);
});

test('user can update profile and preferred vj preferences', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'preferred_language' => 'Luganda',
    ]);

    $vj = Vj::factory()->create(['stage_name' => 'VJ Emmy']);

    $response = $this->actingAs($user)
        ->put('/profile', [
            'name' => 'John K. Musisi',
            'email' => 'john@example.com',
            'preferred_language' => 'Runyankole',
            'preferred_vj_id' => $vj->id,
        ]);

    $response->assertRedirect();

    $user->refresh();
    expect($user->name)->toBe('John K. Musisi');
    expect($user->preferred_language)->toBe('Runyankole');
    expect($user->preferred_vj_id)->toBe($vj->id);
    expect($user->preferredVj->stage_name)->toBe('VJ Emmy');
});
