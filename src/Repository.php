<?php

namespace Veelasky\LaravelHashId;

use ArrayAccess;
use Hashids\Hashids;
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

        return $result[0] ?? null;
    }

    /** {@inheritdoc} */
    public function idToHash(int $idKey, string $key = 'default'): string
    {
        return $this->get($key)->encode($idKey);
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

        if ($key === 'default') {
            return $this->make(
                $key,
                config('hashid.hash_salt') ? config('hashid.hash_salt') : substr(config('app.key', config('hashid.hash_alphabet')), 8, 4).substr(config('app.key', 'lara'), -4)
            );
        }

        $key = strlen($key) > 4 ? $key : 'default'.$key;

        return $this->make($key, config('hashid.hash_salt') ? config('hashid.hash_salt') : substr($key, -4).substr(config('app.key', 'lara'), -4));
    }

    /** {@inheritdoc} */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->hashes);
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
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->hashes[$offset]);
    }
}
