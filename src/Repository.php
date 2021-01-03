<?php

namespace Veelasky\LaravelHashId;

use ArrayAccess;
use Hashids\Hashids;
use Illuminate\Support\Arr;
use Veelasky\LaravelHashId\Contracts\Repository as RepositoryContract;

class Repository implements RepositoryContract, ArrayAccess
{
    /**
     * All registered HashIds Object.
     *
     * @var \Hashids\Hashids[]
     */
    protected $hashes = [];

    /** {@inheritdoc} */
    public function all(): array
    {
        return $this->hashes;
    }

    /** {@inheritdoc} */
    public function hashToId(string $hash, string $key = 'default'): ?int
    {
        $result = $this->get($key)->decode($hash);

        return Arr::first($result);
    }

    /** {@inheritdoc} */
    public function idToHash(int $id, string $key = 'default'): string
    {
        return $this->get($key)->encode($id);
    }

    /** {@inheritdoc} */
    public function make(string $key, string $salt): Hashids
    {
        $hashids = new Hashids($salt, config('hashid.hash_length'), config('hashid.hash_alphabet'));
        $this->set($key, $hashids);

        return $hashids;
    }

    /** {@inheritdoc} */
    public function set(string $key, Hashids $value): RepositoryContract
    {
        $this->hashes[$key] = $value;

        return $this;
    }

    /** {@inheritdoc} */
    public function get(string $key): Hashids
    {
        if ($this->has($key)) {
            return $this->hashes[$key];
        }

        $key = strlen($key) > 4 ? $key : 'default'.$key;

        return $this->make($key, substr($key, -4).substr(config('app.key', 'lara'), -4));
    }

    /** {@inheritdoc} */
    public function has(string $key): bool
    {
        return Arr::has($this->hashes, $key);
    }

    /** {@inheritdoc} */
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    /** {@inheritdoc} */
    public function offsetGet($offset): Hashids
    {
        return $this->get($offset);
    }

    /** {@inheritdoc} */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset($offset)
    {
        unset($this->hashes[$offset]);
    }
}
