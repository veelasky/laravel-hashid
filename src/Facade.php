<?php

namespace Veelasky\LaravelHashId;

/**
 * @method static \Hashids\Hashids[]|array           all()
 * @method static int|null                           hashToId(string $hash, string $key = 'default')
 * @method static string                             idToHash(int $id, string $key = 'default')
 * @method static \Hashids\Hashids                   make(string $key, string $salt)
 * @method static \Veelasky\LaravelHashId\Repository set(string $key, \Hashids\Hashids $hashids)
 * @method static \Hashids\Hashids                   get(string $key)
 * @method static bool                               has(string $key)
 */
class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'app.hashid';
    }
}
