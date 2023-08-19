<?php

namespace Tests\Unit;

use Hashids\Hashids;
use Illuminate\Support\Str;
use Tests\TestCase;
use Veelasky\LaravelHashId\Repository;

class RepositoryTest extends TestCase
{
    public function test_automatically_create()
    {
        $randomId = rand(1, 1000);
        $key = Str::random();
        $hashedId = $this->getRepository()->idToHash($randomId, $key);

        $this->assertInstanceOf(Hashids::class, $this->getRepository()->get($key));
        $this->assertArrayHasKey($key, $this->getRepository()->all());

        // assert result
        $this->assertIsString($hashedId);
        $this->assertEquals($randomId, $this->getRepository()->hashToId($hashedId, $key));
    }

    public function test_result_unchanged()
    {
        $key = Str::random();
        $hashId = $this->getRepository()->get($key);

        $this->assertEquals($hashId, $this->getRepository()->get($key));
        $this->assertEquals($hashId->encode(666), $this->getRepository()->idToHash(666, $key));
    }

    public function test_array_access()
    {
        $this->assertIsArray($this->getRepository()->all());

        $key = Str::random();
        $hashid = $this->getRepository()[$key];

        $this->assertEquals($hashid, $this->getRepository()->get($key));

        $this->getRepository()->offsetSet($key, $hashid);
        $this->assertEquals($hashid, $this->getRepository()->get($key));

        $this->assertArrayHasKey($key, $this->getRepository());

        unset($this->getRepository()[$key]);
        $this->assertArrayNotHasKey($key, $this->getRepository());
    }

    public function test_should_have_default()
    {
        $default = $this->getRepository()->get('default');

        $this->assertInstanceOf(Hashids::class, $default);

        $this->assertEquals([1234], $default->decode('jXrJE9PL'));
    }

    protected function getRepository(): Repository
    {
        return app('app.hashid');
    }
}
