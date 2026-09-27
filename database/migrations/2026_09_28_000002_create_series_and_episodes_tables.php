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
        // 1. TV Series
        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('synopsis')->nullable();
            $table->longText('description')->nullable();
            $table->string('poster')->nullable();
            $table->string('backdrop')->nullable();
            $table->foreignId('vj_id')->nullable()->constrained('vjs')->nullOnDelete();
            $table->foreignId('language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->unsignedSmallInteger('first_air_year')->nullable()->default(2023);
            $table->string('status')->default('published'); // published, draft, archived
            $table->boolean('featured')->default(false);
            $table->boolean('trending')->default(false);
            $table->unsignedBigInteger('views')->default(0);
            $table->decimal('average_rating', 3, 2)->default(4.80);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 2. Pivot: genre_series
        Schema::create('genre_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['series_id', 'genre_id']);
        });

        // 3. Seasons
        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')->constrained('series')->cascadeOnDelete();
            $table->unsignedSmallInteger('season_number')->default(1);
            $table->string('title')->nullable();
            $table->text('overview')->nullable();
            $table->string('poster')->nullable();
            $table->unsignedSmallInteger('release_year')->nullable();
            $table->timestamps();

            $table->unique(['series_id', 'season_number']);
        });

        // 4. Episodes
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->unsignedSmallInteger('episode_number');
            $table->string('title');
            $table->text('overview')->nullable();
            $table->string('video_url')->nullable();
            $table->string('video_path')->nullable();
            $table->unsignedSmallInteger('duration')->nullable()->default(45); // in minutes
            $table->string('thumbnail')->nullable();
            $table->foreignId('vj_id')->nullable()->constrained('vjs')->nullOnDelete();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('is_free')->default(false);
            $table->timestamps();

            $table->unique(['season_id', 'episode_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('genre_series');
        Schema::dropIfExists('series');
    }
};
