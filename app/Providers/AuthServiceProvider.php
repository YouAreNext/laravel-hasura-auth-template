<?php

namespace App\Providers;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
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
        $this->app['auth']->viaRequest('jwt', function ($request) {
            $token = $request->get('token') ?? $request->bearerToken();
            if ($token) {
                try {
                    $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
                    $model = $credentials->model;

                    $user = $model::findOrFail($credentials->sub);

                    return $user;
                } catch (Exception $e) {
                    throw new Exception($e);
                }
            }
        });
    }
}
