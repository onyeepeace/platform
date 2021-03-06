<?php

namespace Ushahidi\App\Providers;

use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use League\OAuth2\Server\ResourceServer;
use League\OAuth2\Server\Grant\PasswordGrant;
use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use Ushahidi\App\Passport\TokenGuard;

use Illuminate\Auth\RequestGuard;

// use Illuminate\Auth\Events\Logout;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Event;
// use Illuminate\Support\Facades\Cookie;
// use Illuminate\Support\Facades\Request;
// use Laravel\Passport\Guards\TokenGuard;
// use Illuminate\Support\ServiceProvider;
// use League\OAuth2\Server\AuthorizationServer;
// use League\OAuth2\Server\Grant\AuthCodeGrant;
// use League\OAuth2\Server\Grant\ImplicitGrant;
// use League\OAuth2\Server\Grant\PasswordGrant;
// use Laravel\Passport\Bridge\PersonalAccessGrant;
// use League\OAuth2\Server\Grant\RefreshTokenGrant;
// use Laravel\Passport\Bridge\RefreshTokenRepository;
// use League\OAuth2\Server\Grant\ClientCredentialsGrant;

class PassportServiceProvider extends LaravelPassportServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \Laravel\Passport\ClientRepository::class,
            \Ushahidi\App\Passport\ClientRepository::class
        );

        $this->app->bind(
            \Laravel\Passport\Bridge\UserRepository::class,
            \Ushahidi\App\Passport\UserRepository::class
        );

        $this->app->bind(
            \Laravel\Passport\Bridge\RefreshTokenRepository::class,
            \Ushahidi\App\Passport\RefreshTokenRepository::class
        );

        parent::boot();
    }

    /**
     * Make an instance of the token guard.
     *
     * @param  array  $config
     * @return RequestGuard
     */
    protected function makeGuard(array $config)
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard(
                $this->app->make(ResourceServer::class),
                service('repository.user'),
                // Auth::createUserProvider($config['provider']),
                $this->app->make(TokenRepository::class),
                $this->app->make(ClientRepository::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }
}
