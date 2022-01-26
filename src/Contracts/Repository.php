<?php

namespace Veelasky\LaravelHashId\Contracts;

use Hashids\Hashids;

interface Repository
{
    /**
     * Create new HashId Instance.
     *
     * @param string $key
     * @param string $salt
     *
     * @return \Hashids\Hashids;
     */
    public function make(string $key, string $salt): Hashids;

    /**
     * Determine if the given HashId value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Get the specified HashId object.
     *
     * @param string $key
     *
     * @return Hashids
     */
    public function get(string $key): Hashids;

    /**
     * Get all of the HashId items.
     *
     * @return array|\Hashids\Hashids[]
     */
    public function all(): array;

    /**
     * Set a given HashId object.
     *
     * @param string           $key
     * @param \Hashids\Hashids $value
     *
     * @return \Veelasky\LaravelHashId\Contracts\Repository
     */
    public function set(string $key, Hashids $value): self;

    /**
     * Convert hash to id.
     *
     * @param string      $hash
     * @param string|null $key
     *
     * @return int|null
     */
    public function hashToId(string $hash, string $key = 'default'): ?int;

    /**
     * Convert id to Hash.
     *
     * @param int         $idKey
     * @param string|null $key
     *
     * @return string
     */
    public function idToHash(int $idKey, string $key = 'default'): string;
}
