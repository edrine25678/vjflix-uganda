<?php

namespace Tests\Feature;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Role;
use App\Models\User;
use App\Models\Vj;
use App\Services\TmdbService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminTmdbTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create([
            'name' => 'admin',
            'label' => 'Administrator',
        ]);

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->admin->roles()->attach($adminRole);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
        ]);
    }

    public function test_guest_and_non_admin_cannot_access_tmdb_resources(): void
    {
        $this->get(route('admin.tmdb.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($this->regularUser)
            ->get(route('admin.tmdb.index'))
            ->assertForbidden();
    }

    public function test_admin_can_access_tmdb_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.tmdb.index'))
            ->assertOk()
            ->assertSee('TMDB Resources', false);
    }

    public function test_admin_can_search_tmdb_via_json_api(): void
    {
        Http::fake([
            'api.themoviedb.org/3/search/movie*' => Http::response([
                'results' => [
                    [
                        'id' => 550,
                        'title' => 'Fight Club',
                        'release_date' => '1999-10-15',
                        'poster_path' => '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                        'overview' => 'An insomniac office worker...',
                        'vote_average' => 8.4,
                    ],
                ],
            ]),
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.tmdb.search', ['query' => 'Fight Club', 'type' => 'movie']));

        $response->assertOk()
            ->assertJsonPath('results.0.id', 550)
            ->assertJsonPath('results.0.title', 'Fight Club');
    }

    public function test_admin_can_fetch_tmdb_movie_details(): void
    {
        Http::fake([
            'api.themoviedb.org/3/movie/550*' => Http::response([
                'id' => 550,
                'title' => 'Fight Club',
                'original_title' => 'Fight Club',
                'release_date' => '1999-10-15',
                'runtime' => 139,
                'overview' => 'An insomniac office worker...',
                'tagline' => 'Mischief. Mayhem. Soap.',
                'poster_path' => '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'backdrop_path' => '/hZkgoQYus5vegHoetLkCJzb17zJ.jpg',
                'vote_average' => 8.43,
                'genres' => [
                    ['id' => 18, 'name' => 'Drama'],
                ],
                'videos' => ['results' => []],
                'production_countries' => [['name' => 'United States']],
                'original_language' => 'en',
            ]),
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson(route('admin.tmdb.details', ['type' => 'movie', 'id' => 550]));

        $response->assertOk()
            ->assertJsonPath('title', 'Fight Club')
            ->assertJsonPath('duration', 139)
            ->assertJsonPath('release_year', 1999);
    }

    public function test_admin_can_import_movie_directly_from_tmdb(): void
    {
        $vj = Vj::factory()->create();
        $genre = Genre::create(['name' => 'Drama', 'slug' => 'drama']);

        Http::fake([
            'api.themoviedb.org/3/movie/550*' => Http::response([
                'id' => 550,
                'title' => 'Fight Club',
                'original_title' => 'Fight Club',
                'release_date' => '1999-10-15',
                'runtime' => 139,
                'overview' => 'An insomniac office worker...',
                'tagline' => 'Mischief. Mayhem. Soap.',
                'poster_path' => '/pB8BM7pdSp6B6Ih7QZ4DrQ3PmJK.jpg',
                'backdrop_path' => '/hZkgoQYus5vegHoetLkCJzb17zJ.jpg',
                'vote_average' => 8.43,
                'genres' => [
                    ['id' => 18, 'name' => 'Drama'],
                ],
                'videos' => ['results' => []],
                'production_countries' => [['name' => 'United States']],
                'original_language' => 'en',
            ]),
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.tmdb.import'), [
                'tmdb_id' => 550,
                'type' => 'movie',
                'vj_id' => $vj->id,
                'status' => 'draft',
            ]);

        $movie = Movie::where('title', 'Fight Club (Luganda)')->first();
        $this->assertNotNull($movie);
        $this->assertEquals(139, $movie->duration);
        $this->assertEquals(1999, $movie->release_year);
        $this->assertEquals('draft', $movie->status);
        $this->assertEquals($vj->id, $movie->vj_id);

        $response->assertRedirect(route('admin.movies.edit', $movie->id));
    }
}
