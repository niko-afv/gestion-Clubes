<?php

namespace App\Providers;

use function foo\func;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('list-users', function ($user){
            return isAdmin( $user );
        });

        Gate::define('list-units', function ($user){
            return ( isAdmin( $user ) || isFieldLeader($user) || isZoneLeader($user) );
        });

        Gate::define('crud-units', function ($user){
            return ( isClubLeader($user) );
        });

        Gate::define('list-events', function ($user){
            return true;
        });

        Gate::define('crud-events', function ($user){
            return ( isAdmin( $user ) || isFieldLeader($user) || isZoneLeader($user) );
        });

        Gate::define('list-clubs', function ($user){
            return ( isAdmin( $user ) || isFieldLeader($user) || isZoneLeader($user) );
        });

        Gate::define('see-my-club', function ($user){
            return isClubLeader( $user );
        });

        Gate::define('make-payment', function ($user){
            return isClubLeader( $user );
        });

        Gate::define('crud-club-members', function ($user){
            return isClubLeader( $user );
        });

        Gate::define('crud-field-members', function ($user){
            return ( isAdmin($user) || isFieldLeader( $user ) );
        });

        Gate::define('verify-payment', function ($user){
            return ( isAdmin($user) || isFieldLeader( $user ) );
        });

        Gate::define('add-club-director', function ($user){
            return ( isAdmin($user) || isFieldLeader( $user ) || isZoneLeader($user) );
        });

        Gate::define('see-my-field', function ($user){
            return ( isAdmin( $user ) || isFieldLeader($user) || isZoneLeader($user) );
        });

        Gate::define('sync-data', function ($user){
            return isAdmin( $user );
        });
    }
}
