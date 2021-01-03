<?php

namespace Veelasky\LaravelHashId\Contracts;

use Illuminate\Contracts\Config\Repository as ConfigInterface;

/**
 * HashId Repository Contract.
 *
 * @author          veelasky <veelasky@gmail.com>
 */
interface Repository extends ConfigInterface
{
    /**
     * Create new HashId Instance.
     *
     * @param string $key
     * @param string $salt
     *
     * @return \Hashids\Hashids;
     */
    public function make($key, $salt);
}
