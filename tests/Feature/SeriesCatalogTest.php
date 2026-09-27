<?php

use App\Models\Episode;
use App\Models\Season;
use App\Models\Series;
use App\Models\Vj;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can render series catalog page', function () {
    $vj = Vj::factory()->create(['stage_name' => 'VJ Junior']);
    $series = Series::factory()->create([
        'title' => 'Money Heist (Luganda)',
        'vj_id' => $vj->id,
        'status' => 'published',
    ]);

    $response = $this->get('/series');

    $response->assertStatus(200);
    $response->assertSee('Money Heist (Luganda)');
    $response->assertSee('VJ Junior');
});

test('can view series details page with seasons and episodes', function () {
    $vj = Vj::factory()->create(['stage_name' => 'VJ Jingo']);
    $series = Series::factory()->create([
        'title' => 'Prison Break (Luganda)',
        'slug' => 'prison-break-luganda',
        'vj_id' => $vj->id,
        'status' => 'published',
    ]);

    $season = Season::factory()->create([
        'series_id' => $series->id,
        'season_number' => 1,
        'title' => 'Season 1: Fox River',
    ]);

    $episode = Episode::factory()->create([
        'season_id' => $season->id,
        'episode_number' => 1,
        'title' => 'Pilot Episode (Luganda)',
    ]);

    $response = $this->get('/series/prison-break-luganda');

    $response->assertStatus(200);
    $response->assertSee('Prison Break (Luganda)');
    $response->assertSee('VJ Jingo');
    $response->assertSee('Pilot Episode (Luganda)');
});

test('can watch episode on watch page', function () {
    $vj = Vj::factory()->create(['stage_name' => 'VJ Emmy']);
    $series = Series::factory()->create([
        'title' => 'Squid Game (Luganda)',
        'slug' => 'squid-game-luganda',
        'vj_id' => $vj->id,
        'status' => 'published',
    ]);

    $season = Season::factory()->create([
        'series_id' => $series->id,
        'season_number' => 1,
    ]);

    $episode = Episode::factory()->create([
        'season_id' => $season->id,
        'episode_number' => 1,
        'title' => 'Red Light Green Light',
        'vj_id' => $vj->id,
    ]);

    $response = $this->get('/series/squid-game-luganda/season/1/episode/1');

    $response->assertStatus(200);
    $response->assertSee('Squid Game (Luganda)');
    $response->assertSee('Red Light Green Light');
    $response->assertSee('VJ Emmy');
    $response->assertSee('vjflix-player');
});
