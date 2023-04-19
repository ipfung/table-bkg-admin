<?php

namespace App\Providers;

use App\Facade\PermissionService;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(PermissionService $permissionService)
    {
        $this->registerPolicies();

        //
        Gate::define('room', function (User $user) use ($permissionService) {
            return $permissionService->isInternalCoachLevel($user);
        });
        Gate::define('packages', function (User $user) use ($permissionService) {
            return $permissionService->isInternalCoachLevel($user);
        });
    }
}
