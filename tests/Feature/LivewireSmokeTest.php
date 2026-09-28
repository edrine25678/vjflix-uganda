<?php

namespace Tests\Feature;

use App\Livewire\AdminController;
use App\Livewire\SearchVelflix;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_component_resolves_and_renders(): void
    {
        Livewire::test(SearchVelflix::class)
            ->assertOk()
            ->set('searchVelflix', 'zzzznomatch')
            ->assertOk();
    }

    public function test_admin_component_resolves_and_renders(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        Livewire::actingAs($admin)
            ->test(AdminController::class)
            ->assertOk();
    }
}
