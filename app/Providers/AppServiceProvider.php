<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('admin', fn (User $user) => $user->isAdmin());
        Gate::define('manage-content', fn (User $user) => $user->isAdmin() || $user->hasRole('content_manager'));
        Gate::define('manage-vjs', fn (User $user) => $user->isAdmin() || $user->hasRole('vj_manager'));
        Gate::define('watch-premium', fn (User $user) => $user->isAdmin() || $user->hasRole('subscriber'));
    }
}
