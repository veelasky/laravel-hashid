<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use Tests\Models\BasicModel;
use Tests\Models\CustomKeyModel;
use Tests\Models\CustomSaltModel;
use Tests\Models\HashModel;
use Tests\Models\IllegalHashModel;
use Tests\Models\PersistingModel;
use Tests\Models\PersistingModelWithCustomName;
use Tests\TestCase;
use Veelasky\LaravelHashId\Repository;
use Veelasky\LaravelHashId\Rules\ExistsByHash;

class HashableIdModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_illegal_hash_model()
    {
        $this->expectExceptionMessage('Invalid implementation of HashId, only works with `int` value of `keyType`');
        IllegalHashModel::idToHash(1);
    }

    public function test_hash_model()
    {
        $randomId = rand(1, 1000);
        $hashedId = HashModel::idToHash($randomId);

        // assert using model.
        $this->assertEquals($randomId, HashModel::hashToId($hashedId));

        // assert using repo.
        $this->assertEquals($randomId, $this->getRepository()->hashToId($hashedId, HashModel::class));
        $this->assertEquals($hashedId, $this->getRepository()->idToHash($randomId, HashModel::class));
    }

    public function test_model_not_persisting()
    {
        $m = new HashModel();
        $m->save();

        $this->assertDatabaseMissing($m->getTable(), [
            'hashid' => HashModel::idToHash($m->id),
        ]);
        $this->assertDatabaseHas($m->getTable(), [
            'id' => $m->id,
        ]);

        $this->assertEquals(HashModel::idToHash($m->getKey()), $m->hash);

        $t = HashModel::byHash($m->hash);
        $this->assertEquals($t->id, $m->id);

        $this->expectException(ModelNotFoundException::class);
        HashModel::byHashOrFail(Str::random(8));
    }

    public function test_model_persistence()
    {
        $m = new PersistingModel();
        $m->save();

        $this->assertDatabaseHas($m->getTable(), [
            'id'     => $m->id,
            'hashid' => $m->hash,
        ]);

        $this->assertDatabaseMissing($m->getTable(), [
            'id'          => $m->id,
            'custom_name' => $m->hash,
        ]);

        $this->assertEquals($m->hashid, $m->hash);

        $t = PersistingModel::byHash($m->hash);
        $this->assertEquals($t->id, $m->id);

        $this->expectException(ModelNotFoundException::class);
        PersistingModel::byHashOrFail(Str::random(8));
    }

    public function test_model_persistence_with_column_name()
    {
        $m = new PersistingModelWithCustomName();
        $m->save();

        $this->assertDatabaseHas($m->getTable(), [
            'id'          => $m->id,
            'custom_name' => $m->hash,
        ]);

        $this->assertDatabaseMissing($m->getTable(), [
            'id'     => $m->id,
            'hashid' => $m->hash,
        ]);

        $this->assertEquals($m->custom_name, $m->hash);

        $t = PersistingModelWithCustomName::byHash($m->hash);
        $this->assertEquals($t->id, $m->id);

        $this->expectException(ModelNotFoundException::class);
        PersistingModelWithCustomName::byHashOrFail(Str::random(8));
    }

    public function test_validation_rules()
    {
        $m = new HashModel();
        $m->save();

        $this->assertDatabaseHas($m->getTable(), [
            'id' => $m->id,
        ]);

        $validator = Validator::make([
            'id' => $m->hash,
        ], [
            'id' => [new ExistsByHash(HashModel::class)],
        ]);
        $this->assertFalse($validator->fails());

        $validator = Validator::make([
            'id' => Str::random(),
        ], [
            'id' => [new ExistsByHash(HashModel::class)],
        ]);
        $this->assertTrue($validator->fails());

        $this->expectException(ValidationException::class);
        $validator->validate();
    }

    public function test_validation_not_using_trait()
    {
        $this->expectException(InvalidArgumentException::class);

        Validator::make([
            'id' => Str::random(),
        ], [
            'id' => [new ExistsByHash(BasicModel::class)],
        ]);
    }

    public function test_validation_on_persisting_model()
    {
        $m = new PersistingModel();
        $m->save();

        $validator = Validator::make([
            $m->getHashColumnName() => $m->hash,
        ], [
            $m->getHashColumnName() => [new ExistsByHash(PersistingModel::class)],
        ]);
        $this->assertFalse($validator->fails());

        $validator = Validator::make([
            $m->getHashColumnName() => Str::random(),
        ], [
            $m->getHashColumnName() => [new ExistsByHash(PersistingModel::class)],
        ]);
        $this->assertTrue($validator->fails());

        $this->expectException(ValidationException::class);
        $validator->validate();
    }

    public function test_custom_key_model()
    {
        $m = new CustomKeyModel();
        $m->save();

        $this->assertEquals('somethingUnique', $m->getHashKey());
        $this->assertEquals(CustomKeyModel::idToHash($m->getKey()), $m->hash);
    }

    public function test_custom_salt_model()
    {
        $this->getRepository()->make('custom', (new CustomSaltModel())->getHashIdSalt());
        $this->assertEquals(CustomSaltModel::idToHash(1), $this->getRepository()->idToHash(1, 'custom'));
    }

    public function test_route_binding()
    {
        $m = new HashModel();
        $m->save();

        $resolved = $m->resolveRouteBinding($m->hash);
        $this->assertTrue($resolved->is($m));

        $resolved = $m->resolveRouteBinding($m->getKey());
        $this->assertTrue($resolved->is($m));
    }

    protected function getRepository(): Repository
    {
        return app('app.hashid');
    }
}
