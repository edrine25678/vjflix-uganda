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
        // Add preferred_vj_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('preferred_vj_id')->nullable()->after('preferred_language')->constrained('vjs')->nullOnDelete();
        });

        // Watchlist ("My List") table
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('watchable_type');
            $table->unsignedBigInteger('watchable_id');
            $table->timestamps();

            $table->unique(['user_id', 'watchable_type', 'watchable_id']);
            $table->index(['watchable_type', 'watchable_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Ratings & Reviews table
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');
            $table->unsignedTinyInteger('rating'); // 1 to 5 stars
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->unique(['user_id', 'reviewable_type', 'reviewable_id']);
            $table->index(['reviewable_type', 'reviewable_id', 'is_approved']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('watchlists');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_vj_id']);
            $table->dropColumn('preferred_vj_id');
        });
    }
};
