<?php

namespace Veelasky\LaravelHashId\Eloquent;

use Veelasky\LaravelHashId\Repository as HashRepository;

/**
 * Eloquent Model Hashable Id.
 *
 * @author      veelasky <veelasky@gmail.com>
 *
 * @method static \Illuminate\Database\Eloquent\Model|object|static|null byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Model|static byHashOrFail(string $hash)
 */
trait HashableId
{
    /**
     * Get Model by hashed key.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $hash
     *
     * @return mixed
     */
    public function scopeByHash($query, $hash)
    {
        return $query->where($this->getKeyName(), self::hashToId($hash))
            ->first();
    }

    /**
     * Get Model by hashed key or fail.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $hash
     *
     * @return mixed
     */
    public function scopeByHashOrFail($query, $hash)
    {
        return $query->where($this->getKeyName(), self::hashToId($hash))
            ->firstOrFail();
    }

    /**
     * Get Hash Attribute.
     *
     * @return string
     */
    public function getHashAttribute()
    {
        return $this->hashIds()
            ->encode($this->getOriginal($this->getKeyName()));
    }

    /**
     * Decode Hash to ID for the model.
     *
     * @param $hash
     * @return mixed
     */
    public static function hashToId($hash)
    {
        $result = with(new static)->hashIds()->decode($hash);

        return (is_array($result) and isset($result[0])) ? $result[0] : null;
    }

    /**
     * Encode Id to Hash for the model.
     *
     * @param $id
     * @return mixed
     */
    public static function idToHash($id)
    {
        return with(new static)->hashIds()->encode($id);
    }

    /**
     * Get HashIds Implementation.
     *
     * @return \Hashids\Hashids
     */
    protected function hashIds()
    {
        /*
         * HashId Repository class.
         *
         * @var \Veelasky\LaravelHashId\Repository
         */
        $repository = app(HashRepository::class);
        // if it already exists on repository let's throw them
        if ($repository->has($this->getTable())) {
            return $repository->get($this->getTable());
        }
        $salted = substr(strrev(self::class), 0, 4).substr(env('APP_KEY'), 0, 4);
        // ... create a new hashid instance if it not existed
        $hash = $repository->make($this->getTable(), $salted);

        return $hash;
    }
}
