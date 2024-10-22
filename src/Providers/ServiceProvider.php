<?php

namespace SMSkin\LaravelTgWebAppAuth\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use SMSkin\LaravelTgWebAppAuth\Contracts\IUserRepository;
use SMSkin\LaravelTgWebAppAuth\Repositories\UserRepository;
use SMSkin\LaravelTgWebAppAuth\TelegramUserGuard;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->bind(IUserRepository::class, UserRepository::class);
    }

    public function boot()
    {
        Auth::extend('tgwebapp', static function (Application $app, string $name, array $config) {
            return new TelegramUserGuard(
                $app['request'],
                $app->make(IUserRepository::class),
                $config['token'],
                $config['autoCreation']
            );
        });
    }
}
