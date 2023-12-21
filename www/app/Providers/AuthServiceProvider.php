<?php

namespace App\Providers;

use App\Services\System\RoutineActionService;
use App\Services\System\UserService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(Request $request): void
    {
        $this->registerPolicies();
        Gate::define('system_routine', function ($user, $actionSlug) use ($request) {
            if ($user->is_master) {
                return true;
            }
            $request->attributes->add([
                'action_slug'   =>    $actionSlug,
            ]);
            return UserService::checkUserPermissionByActionSlug($user, $actionSlug);
        });
    }
}
