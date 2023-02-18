<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        \Gate::define('administer', fn(WorkspaceAccount $account) => $account->role === WorkspaceAccountRole::Administrator->value);
        \Gate::define('editor', fn(WorkspaceAccount $account) => $account->role === WorkspaceAccountRole::Editor->value);
        \Gate::define('viewer', fn(WorkspaceAccount $account) => $account->role === WorkspaceAccountRole::Viewer->value);
        \Gate::define('guest', fn(WorkspaceAccount $account) => $account->role === WorkspaceAccountRole::Guest->value);
    }
}
