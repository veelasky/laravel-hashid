<?php

namespace Tests\Unit;

use Tests\TestCase;
use Veelasky\LaravelHashId\Facade;
use Veelasky\LaravelHashId\Repository;

class ProviderTest extends TestCase
{
    public function test_provider_is_loaded_correctly()
    {
        $this->assertInstanceOf(Repository::class, app('app.hashid'));
        $this->assertInstanceOf(Repository::class, app(Repository::class));
        $this->assertInstanceOf(Repository::class, app(\Veelasky\LaravelHashId\Contracts\Repository::class));
    }

    public function test_provider_load_config_files()
    {
        $this->assertEquals(8, config('hashid.hash_length'));
        $this->assertEquals('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', config('hashid.hash_alphabet'));
    }

    public function test_facade()
    {
        $this->assertEquals(Facade::get('default'), app('app.hashid')->get('default'));
    }
}
