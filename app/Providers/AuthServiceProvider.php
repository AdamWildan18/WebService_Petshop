<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
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
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.


        Gate::define('store', function ($user){
            if ($user->role == 'admin' || $user->role == 'editor') {
                return true;
            }else{
                return false;
            }
        });
        Gate::define('update', function ($user, $product){
            if ($user->role == 'admin') {
                return true;
            }else if ($user->role == 'editor') {
                return $product->user_id == $user->id;
            }else{
                return false;
            }
        });

        Gate::define('delete', function ($user, $product){
            if ($user->role == 'admin') {
                return true;
            }else if ($user->role == 'editor') {
                return $product->user_id == $user->id;
            }else{
                return false;
            }
        });

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('api_token')) {
                return User::where('api_token', $request->input('api_token'))->first();
            }
        });
    }
}
