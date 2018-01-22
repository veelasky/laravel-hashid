<?php

namespace Veelasky\LaravelHashId;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Veelasky\LaravelHashId\Contracts\Repository as RepositoryContract;

/**
 * HashId Service Provider.
 *
 * @author      veelasky <veelasky@gmail.com>
 */
class HashIdServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['app.hashid']->make('root', env('APP_KEY'));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->singleton('app.hashid', function () {
            $repository = new Repository();

            return $repository;
        });
        $this->app->alias('app.hashid', Repository::class);
        $this->app->alias('app.hashid', RepositoryContract::class);
    }
}

