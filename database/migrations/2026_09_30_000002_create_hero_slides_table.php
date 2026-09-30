<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('hero_slides')) {
            Schema::create('hero_slides', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('vj_name')->nullable();
                $table->string('release_year', 10)->nullable();
                $table->string('rating', 10)->nullable();
                $table->string('badge_text')->nullable()->default('HD Luganda');
                $table->text('synopsis')->nullable();
                $table->string('backdrop_url')->nullable();
                $table->string('backdrop_path')->nullable();
                $table->string('watch_url')->nullable();
                $table->string('download_url')->nullable();
                $table->unsignedBigInteger('movie_id')->nullable();
                $table->unsignedBigInteger('series_id')->nullable();
                $table->unsignedTinyInteger('sort_order')->default(1);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
