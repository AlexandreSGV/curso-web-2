<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Acesso geral à administração do catálogo.
        Gate::define('manage-catalog', function (User $user): bool {
            return $user->role === 'admin';
        });
    }
}
