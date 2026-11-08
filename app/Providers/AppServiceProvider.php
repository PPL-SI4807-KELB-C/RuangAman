<?php

namespace App\Providers;

use App\Models\PenangananKasus;
use App\Models\StatusKasus;
use App\Models\User;
use App\Policies\PenangananKasusPolicy;
use App\Policies\StatusKasusPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        Gate::policy(StatusKasus::class, StatusKasusPolicy::class);
        Gate::policy(PenangananKasus::class, PenangananKasusPolicy::class);

        Gate::define('admin-kasus', fn (User $user) => $user->isAdminKasus());
        Gate::define('kelola-status-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-status-kasus'));
        Gate::define('kelola-penugasan-kasus', fn (User $user) => $user->isAdminKasus() || $user->hasPermission('kelola-penugasan-kasus'));
    }
}
