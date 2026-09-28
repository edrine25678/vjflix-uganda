<?php

use App\Models\Genre;
use App\Models\Movie;
use App\Models\User;
use App\Models\Vj;
use App\Services\AnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can render VJ discovery page', function () {
    $vj = Vj::factory()->create([
        'stage_name' => 'VJ Junior',
        'is_active' => true,
    ]);

    $response = $this->get('/vjs');

    $response->assertStatus(200);
    $response->assertSee('VJ Junior');
    $response->assertSee("Uganda's Legendary", false);
});

test('can view VJ profile page with translated movies', function () {
    $vj = Vj::factory()->create([
        'stage_name' => 'VJ Jingo',
        'slug' => 'vj-jingo',
    ]);

    $movie = Movie::factory()->create([
        'vj_id' => $vj->id,
        'title' => 'Ip Man Final (Luganda)',
        'status' => 'published',
    ]);

    $response = $this->get('/vjs/vj-jingo');

    $response->assertStatus(200);
    $response->assertSee('VJ Jingo');
    $response->assertSee('Ip Man Final (Luganda)');
});

test('authenticated user can view movies streaming catalog', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create(['stage_name' => 'VJ Emmy']);
    $movie = Movie::factory()->create([
        'vj_id' => $vj->id,
        'title' => 'The Roundup (Luganda)',
        'status' => 'published',
        'trending' => true,
    ]);

    $response = $this->actingAs($user)->get('/movies');

    $response->assertStatus(200);
    $response->assertSee('The Roundup (Luganda)');
    $response->assertSee('VJ Emmy');
});

test('authenticated user can view movie details page', function () {
    $user = User::factory()->create();
    $vj = Vj::factory()->create(['stage_name' => 'VJ Junior']);
    $genre = Genre::factory()->create(['name' => 'Action']);
    $movie = Movie::factory()->create([
        'vj_id' => $vj->id,
        'title' => 'John Wick 4 (Luganda)',
        'slug' => 'john-wick-4-luganda',
        'status' => 'published',
    ]);
    $movie->genres()->attach($genre);

    $response = $this->actingAs($user)->get('/movie/'.$movie->slug);

    $response->assertStatus(200);
    $response->assertSee('John Wick 4 (Luganda)');
    $response->assertSee('VJ Junior');
    $response->assertSee('STREAM IN LUGANDA');
});

test('non-admin user cannot access admin dashboard', function () {
    $regularUser = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($regularUser)->get('/admin');

    $response->assertStatus(403);
});

test('admin user can access admin dashboard', function () {
    $adminUser = User::factory()->create(['role' => 'super_admin']);

    // Mock the AnalyticsService
    $analyticsServiceMock = Mockery::mock(AnalyticsService::class);
    $analyticsServiceMock->shouldReceive('getPlatformStats')->andReturn([
        'total_users' => 10,
        'active_subscriptions' => 5,
        'total_revenue' => 50000,
    ]);
    $this->app->instance(AnalyticsService::class, $analyticsServiceMock);

    $response = $this->actingAs($adminUser)->get('/admin');

    $response->assertStatus(200);
});
