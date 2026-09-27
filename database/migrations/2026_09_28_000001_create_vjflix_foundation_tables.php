<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add roles and permissions
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });

        // Extend users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email');
            $table->string('profile_photo')->nullable()->after('password');
            $table->string('preferred_language')->default('Luganda')->after('profile_photo');
            $table->boolean('is_active')->default(true)->after('preferred_language');
        });

        // VJs (Ugandan Video Jockeys)
        Schema::create('vjs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stage_name')->index();
            $table->text('biography')->nullable();
            $table->string('profile_photo')->nullable();
            $table->string('cover_photo')->nullable();
            $table->string('specialization')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('views_count')->default(0);
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->timestamps();
        });

        // Genres
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Languages
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Movies Catalog
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title')->index();
            $table->string('slug')->unique();
            $table->string('original_title')->nullable();
            $table->text('description')->nullable();
            $table->text('synopsis')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('video_path')->nullable();
            $table->unsignedInteger('duration')->nullable()->comment('Duration in minutes');
            $table->unsignedSmallInteger('release_year')->nullable()->index();
            $table->string('original_language', 10)->default('en');
            $table->string('translated_language', 50)->default('Luganda');
            $table->foreignId('vj_id')->nullable()->constrained('vjs')->nullOnDelete();
            $table->string('country_of_origin')->default('United States');
            $table->string('age_rating', 10)->default('PG-13');
            $table->string('status', 20)->default('published')->index();
            $table->boolean('featured')->default(false)->index();
            $table->boolean('trending')->default(false)->index();
            $table->unsignedBigInteger('views')->default(0)->index();
            $table->decimal('average_rating', 3, 2)->default(0.00)->index();
            $table->unsignedInteger('ratings_count')->default(0);
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        // Movie - Genre pivot
        Schema::create('movie_genre', function (Blueprint $table) {
            $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->primary(['movie_id', 'genre_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movie_genre');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('genres');
        Schema::dropIfExists('vjs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'profile_photo', 'preferred_language', 'is_active']);
        });

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};
