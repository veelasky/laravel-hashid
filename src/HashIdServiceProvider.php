<?php

namespace Veelasky\LaravelHashId;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Veelasky\LaravelHashId\Contracts\Repository as RepositoryContract;

class HashIdServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config' => $this->app->basePath('config'),
            ], 'laravel-hashid-config');
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/hashid.php',
            'hashid'
        );

        $this->app->singleton('app.hashid', function () {
            return new Repository();
        });
        $this->app->alias('app.hashid', Repository::class);
        $this->app->alias('app.hashid', RepositoryContract::class);
    }
}
