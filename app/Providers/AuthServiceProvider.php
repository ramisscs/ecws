<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        Gate::define('view-secret', function ($user) {
            return $user->canViewSecrets();
        });

        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });
    }
}
