<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('guest or non-admin cannot generate presigned upload url', function () {
    $response = $this->postJson(route('admin.media.generate-upload-url'), [
        'filename' => 'action-movie.mp4',
        'content_type' => 'video/mp4',
    ]);

    $response->assertStatus(401);

    $user = User::factory()->create(['role' => 'user']);
    $userResponse = $this->actingAs($user)->postJson(route('admin.media.generate-upload-url'), [
        'filename' => 'action-movie.mp4',
        'content_type' => 'video/mp4',
    ]);

    $userResponse->assertStatus(403);
});

test('admin can generate cloudflare r2 presigned upload url', function () {
    Storage::fake('r2');

    $admin = User::factory()->create(['role' => 'super_admin']);

    $response = $this->actingAs($admin)->postJson(route('admin.media.generate-upload-url'), [
        'filename' => 'Extraction-2-Luganda.mp4',
        'content_type' => 'video/mp4',
        'folder' => 'movies',
    ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'status',
        'upload_url',
        'file_key',
        'public_url',
    ]);

    $data = $response->json();
    expect($data['status'])->toBe('success');
    expect($data['file_key'])->toContain('movies/');
    expect($data['file_key'])->toContain('extraction-2-luganda.mp4');
});
