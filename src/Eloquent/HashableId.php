<?php

namespace Veelasky\LaravelHashId\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use LogicException;
use Veelasky\LaravelHashId\Repository;

/**
 * Eloquent Model HashableId trait.
 *
 * @property string $hash
 */
trait HashableId
{
    /**
     * Get Model by hashed key.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string                                $hash
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByHash(Builder $query, string $hash): Builder
    {
        return  $this->shouldHashPersist()
            ? $query->where($this->getHashColumnName(), $hash)
            : $query->where($this->getKeyName(), self::hashToId($hash));
    }

    /**
	 * @see parent
	 */
    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return parent::resolveRouteBinding($value, $field);
        }

        return $this->byHash($value);
    }

    /**
     * Get Model by hash.
     *
     * @param $hash
     *
     * @return self|null
     */
    public static function byHash($hash): ?self
    {
        return self::query()->byHash($hash)->first();
    }

    /**
     * Get model by hash or fail.
     *
     * @param $hash
     *
     * @return self
     *
     * @throw \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function byHashOrFail($hash): self
    {
        return self::query()->byHash($hash)->firstOrFail();
    }

    /**
     * Get Hash Attribute.
     *
     * @return string|null
     */
    public function getHashAttribute(): ?string
    {
        return $this->exists
            ? $this->getHashIdRepository()->idToHash($this->getKey(), $this->getHashKey())
            : null;
    }

    /**
     * Decode Hash to ID for the model.
     *
     * @param string $hash
     *
     * @return int|null
     */
    public static function hashToId(string $hash): ?int
    {
        return (new static())
           ->getHashIdRepository()
           ->hashToId($hash, (new static())->getHashKey());
    }

    /**
     * Get Hash Key.
     *
     * @return string
     */
    public function getHashKey(): string
    {
        return property_exists($this, 'hashKey')
            ? $this->hashKey
            : static::class;
    }

    /**
     * Encode Id to Hash for the model.
     *
     * @param int $primaryKey
     *
     * @return string
     */
    public static function idToHash(int $primaryKey): string
    {
        return (new static())
            ->getHashIdRepository()
            ->idToHash($primaryKey, (new static())->getHashKey());
    }

    /**
     * Determine if hash should persist in database.
     *
     * @return bool
     */
    public function shouldHashPersist(): bool
    {
        return property_exists($this, 'shouldHashPersist')
            ? $this->shouldHashPersist
            : false;
    }

    /**
     * Get HashId column name.
     *
     * @return string
     */
    public function getHashColumnName(): string
    {
        return property_exists($this, 'hashColumnName')
            ? $this->hashColumnName
            : 'hashid';
    }

    /**
     * register boot trait method.
     *
     * @return void
     */
    public static function bootHashableId()
    {
        self::created(function ($model) {
            if ($model->shouldHashPersist()) {
                $model->{$model->getHashColumnName()} = self::idToHash($model->getKey());

                $model->save();
            }
        });
    }

    /**
     * Get HashId Repository.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Veelasky\LaravelHashId\Repository
     */
    protected function getHashIdRepository(): Repository
    {
        if ($this->getKeyType() !== 'int') {
            throw new LogicException('Invalid implementation of HashId, only works with `int` value of `keyType`');
        }

        // get custom salt for the model (if exists)
        if (method_exists($this, 'getHashIdSalt')) {
            // force the repository to make a new instance of hashid.
            app('app.hashid')->make($this->getHashKey(), $this->getHashIdSalt());
        }

        return app('app.hashid');
    }
}
