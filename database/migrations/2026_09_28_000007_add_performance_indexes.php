<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the names of the indexes that already exist on the given table.
     */
    protected function indexNames(string $table): array
    {
        return array_column(Schema::getIndexes($table), 'name');
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->addIndex('movies', ['status', 'published_at'], 'movies_status_published_at_index');
        $this->addIndex('movies', ['status', 'views'], 'movies_status_views_index');
        $this->addIndex('movies', ['vj_id', 'status'], 'movies_vj_status_index');
        $this->addIndex('movies', 'trending', 'movies_trending_index');
        $this->addIndex('movies', 'featured', 'movies_featured_index');

        $this->addIndex('series', ['status', 'published_at'], 'series_status_published_at_index');
        $this->addIndex('series', ['status', 'views'], 'series_status_views_index');
        $this->addIndex('series', ['vj_id', 'status'], 'series_vj_status_index');

        $this->addIndex('episodes', ['season_id', 'episode_number'], 'episodes_season_episode_index');
        $this->addIndex('episodes', 'views', 'episodes_views_index');

        $this->addIndex('watch_progress', ['user_id', 'watchable_type', 'watchable_id'], 'watch_progress_user_watchable_index');
        $this->addIndex('watch_progress', ['user_id', 'updated_at'], 'watch_progress_user_updated_index');
        $this->addIndex('watch_progress', 'completed', 'watch_progress_completed_index');

        $this->addIndex('subscriptions', ['user_id', 'status', 'expires_at'], 'subscriptions_user_status_expires_index');
        $this->addIndex('subscriptions', ['status', 'expires_at'], 'subscriptions_status_expires_index');

        $this->addIndex('payments', ['user_id', 'status'], 'payments_user_status_index');
        $this->addIndex('payments', 'transaction_reference', 'payments_transaction_reference_index');
        $this->addIndex('payments', 'external_reference', 'payments_external_reference_index');
        $this->addIndex('payments', 'status', 'payments_status_index');

        $this->addIndex('vjs', ['is_active', 'is_verified'], 'vjs_active_verified_index');
        $this->addIndex('vjs', 'slug', 'vjs_slug_index');

        $this->addIndex('genres', ['is_active', 'name'], 'genres_active_name_index');
    }

    /**
     * Add an index to a table unless an index with the same name already exists.
     */
    protected function addIndex(string $table, string|array $columns, string $name): void
    {
        if (in_array($name, $this->indexNames($table), true)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
            $blueprint->index($columns, $name);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        $indexes = [
            'movies' => [
                'movies_status_published_at_index',
                'movies_status_views_index',
                'movies_vj_status_index',
                'movies_trending_index',
                'movies_featured_index',
            ],
            'series' => [
                'series_status_published_at_index',
                'series_status_views_index',
                'series_vj_status_index',
            ],
            'episodes' => [
                'episodes_season_episode_index',
                'episodes_views_index',
            ],
            'watch_progress' => [
                'watch_progress_user_watchable_index',
                'watch_progress_user_updated_index',
                'watch_progress_completed_index',
            ],
            'subscriptions' => [
                'subscriptions_user_status_expires_index',
                'subscriptions_status_expires_index',
            ],
            'payments' => [
                'payments_user_status_index',
                'payments_transaction_reference_index',
                'payments_external_reference_index',
                'payments_status_index',
            ],
            'vjs' => [
                'vjs_active_verified_index',
                'vjs_slug_index',
            ],
            'genres' => [
                'genres_active_name_index',
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            $existing = $this->indexNames($table);

            $drop = array_values(array_intersect($tableIndexes, $existing));

            if ($drop === []) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($drop): void {
                foreach ($drop as $index) {
                    $blueprint->dropIndex($index);
                }
            });
        }
    }
};
