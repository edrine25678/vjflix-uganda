<?php

use App\Models\Movie;
use App\Models\Season;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can access dashboard and view statistics', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    Movie::factory()->count(3)->create();
    Vj::factory()->count(2)->create();

    // Mock the AnalyticsService
    $analyticsServiceMock = Mockery::mock(AnalyticsService::class);
    $analyticsServiceMock->shouldReceive('getPlatformStats')->andReturn([
        'total_users' => 10,
        'active_subscriptions' => 5,
        'total_revenue' => 50000,
    ]);
    $this->app->instance(AnalyticsService::class, $analyticsServiceMock);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Platform Overview');
    $response->assertSee('Recent Movies', false);
    $response->assertSee('Dashboard');
});

test('non-admin user is rejected from admin cms routes', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)->get('/admin')->assertStatus(403);
    $this->actingAs($user)->get('/admin/movies')->assertStatus(403);
    $this->actingAs($user)->get('/admin/vjs')->assertStatus(403);
    $this->actingAs($user)->get('/admin/series')->assertStatus(403);
});

test('admin can create a new movie translation', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $vj = Vj::factory()->create(['stage_name' => 'VJ Junior']);

    $response = $this->actingAs($admin)
        ->post('/admin/movies', [
            'title' => 'The Equalizer 3 (Luganda)',
            'release_year' => 2023,
            'vj_id' => $vj->id,
            'duration' => 109,
            'status' => 'published',
            'synopsis' => 'Robert McCall takes on the Camorra in southern Italy.',
            'video_url' => 'https://example.com/stream.mp4',
        ]);

    $response->assertRedirect('/admin/movies');
    $this->assertDatabaseHas('movies', [
        'title' => 'The Equalizer 3 (Luganda)',
        'vj_id' => $vj->id,
        'video_url' => 'https://example.com/stream.mp4',
    ]);
});

test('admin can create a new video jockey profile', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($admin)
        ->post('/admin/vjs', [
            'stage_name' => 'VJ Sammy',
            'name' => 'Samuel Semakula',
            'specialization' => 'Classic Martial Arts',
            'biography' => 'Pioneer of Luganda translation cinema.',
            'rating' => 4.85,
            'is_verified' => true,
        ]);

    $response->assertRedirect('/admin/vjs');
    $this->assertDatabaseHas('vjs', [
        'stage_name' => 'VJ Sammy',
        'is_verified' => true,
    ]);
});

test('admin can create a series which automatically seeds season 1', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $vj = Vj::factory()->create(['stage_name' => 'VJ Ice P']);

    $response = $this->actingAs($admin)
        ->post('/admin/series', [
            'title' => 'Lupin (Luganda)',
            'first_air_year' => 2021,
            'vj_id' => $vj->id,
            'status' => 'published',
            'synopsis' => 'Gentleman thief Assane Diop sets out to avenge his father.',
        ]);

    $response->assertRedirect('/admin/series');
    $this->assertDatabaseHas('series', [
        'title' => 'Lupin (Luganda)',
        'vj_id' => $vj->id,
    ]);

    $series = Series::where('title', 'Lupin (Luganda)')->first();
    $this->assertDatabaseHas('seasons', [
        'series_id' => $series->id,
        'season_number' => 1,
    ]);
});

test('admin can add episode to season', function () {
    $admin = User::factory()->create(['role' => 'super_admin']);
    $series = Series::factory()->create(['title' => 'Loki (Luganda)']);
    $season = Season::factory()->create(['series_id' => $series->id, 'season_number' => 1]);

    $response = $this->actingAs($admin)
        ->post("/admin/seasons/{$season->id}/episodes", [
            'episode_number' => 1,
            'title' => 'Glorious Purpose (Luganda)',
            'duration' => 51,
            'video_url' => 'https://example.com/loki_e1.mp4',
            'is_free' => true,
        ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('episodes', [
        'season_id' => $season->id,
        'episode_number' => 1,
        'title' => 'Glorious Purpose (Luganda)',
        'is_free' => true,
    ]);
});
