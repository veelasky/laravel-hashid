<?php

namespace Veelasky\LaravelHashId;

use Hashids\Hashids;
use Illuminate\Config\Repository as ConfigRepository;
use Veelasky\LaravelHashId\Contracts\Repository as RepositoryContract;

/**
 * HashId Repository.
 *
 * @author          veelasky <veelasky@gmail.com>
 */
class Repository extends ConfigRepository implements RepositoryContract
{
    /** {@inheritDoc} */
    public function make($key, $salt)
    {
        $hashids = new Hashids($salt, 8, 'abcdefgihjklmnopqrstuvwxyz0123456789');
        $this->set($key, $hashids);

        return $hashids;
    }
}
