<?php

use App\Models\Episode;
use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use App\Models\WatchProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can record movie watch progress via api', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create(['stage_name' => 'VJ Junior']);
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    $response = $this->actingAs($user)->postJson('/api/progress', [
        'watchable_type' => 'movie',
        'watchable_id' => $movie->id,
        'progress_seconds' => 360,
        'duration_seconds' => 7200,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'progress_seconds' => 360,
        'completed' => false,
    ]);

    $this->assertDatabaseHas('watch_progress', [
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
        'progress_seconds' => 360,
        'completed' => false,
    ]);
});

test('watch progress automatically marks completed at 90 percent playback', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    $response = $this->actingAs($user)->postJson('/api/progress', [
        'watchable_type' => 'movie',
        'watchable_id' => $movie->id,
        'progress_seconds' => 6500,
        'duration_seconds' => 7200, // ~90.2%
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'status' => 'success',
        'completed' => true,
    ]);

    $this->assertDatabaseHas('watch_progress', [
        'user_id' => $user->id,
        'watchable_id' => $movie->id,
        'completed' => true,
    ]);
});

test('user can fetch stored watch progress to resume playback', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    WatchProgress::create([
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
        'progress_seconds' => 1240,
        'duration_seconds' => 5400,
        'completed' => false,
        'last_watched_at' => now(),
    ]);

    $response = $this->actingAs($user)->getJson("/api/progress/movie/{$movie->id}");

    $response->assertStatus(200);
    $response->assertJson([
        'has_progress' => true,
        'progress_seconds' => 1240,
        'completed' => false,
    ]);
});

test('unauthenticated users cannot submit watch progress', function () {
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create(['vj_id' => $vj->id]);

    $response = $this->postJson('/api/progress', [
        'watchable_type' => 'movie',
        'watchable_id' => $movie->id,
        'progress_seconds' => 120,
        'duration_seconds' => 6000,
    ]);

    $response->assertStatus(401);
});

test('continue watching shelf displays on movies catalog when user has unfinished media', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create(['stage_name' => 'VJ Jingo']);
    $movie = Movie::factory()->create([
        'title' => 'Extraction 2 (Luganda)',
        'vj_id' => $vj->id,
    ]);

    WatchProgress::create([
        'user_id' => $user->id,
        'watchable_type' => Movie::class,
        'watchable_id' => $movie->id,
        'progress_seconds' => 1800,
        'duration_seconds' => 7200,
        'completed' => false,
        'last_watched_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/movies');

    $response->assertStatus(200);
    $response->assertSee('Continue Watching');
    $response->assertSee('Extraction 2 (Luganda)');
    $response->assertSee('VJ Jingo');
});

test('movie stream endpoint redirects to media url', function () {
    $vj = Vj::factory()->create();
    $movie = Movie::factory()->create([
        'slug' => 'test-stream-movie',
        'vj_id' => $vj->id,
        'video_url' => 'https://example.com/videos/test.mp4',
    ]);

    $response = $this->get('/stream/movie/test-stream-movie');

    $response->assertRedirect('https://example.com/videos/test.mp4');
});

test('series episode stream endpoint redirects to media url', function () {
    $vj = Vj::factory()->create();
    $series = Series::factory()->create([
        'slug' => 'test-series',
        'vj_id' => $vj->id,
    ]);
    $season = Season::factory()->create([
        'series_id' => $series->id,
        'season_number' => 1,
    ]);
    $episode = Episode::factory()->create([
        'season_id' => $season->id,
        'episode_number' => 1,
        'video_url' => 'https://example.com/episodes/s01e01.mp4',
    ]);

    $response = $this->get('/stream/series/test-series/season/1/episode/1');

    $response->assertRedirect('https://example.com/episodes/s01e01.mp4');
});
