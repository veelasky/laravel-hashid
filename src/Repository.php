<?php

namespace Veelasky\LaravelHashId;

use Hashids\Hashids;
use Illuminate\Config\Repository as ConfigRepository;

/**
 * HashId Repository.
 *
 * @author          veelasky <veelasky@gmail.com>
 */
class Repository extends ConfigRepository
{
    /**
     * Create new HashId Instance.
     *
     * @param string $key
     * @param string $salt
     *
     * @return \Hashids\Hashids;
     */
    public function make($key, $salt)
    {
        $hashids = new Hashids($salt, 8, 'abcdefgihjklmnopqrstuvwxyz0123456789');
        $this->set($key, $hashids);

        return $hashids;
    }
}
