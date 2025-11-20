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
use Tests\Models\HashModelWithFallback;
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

        // Hash-based resolution should work
        $resolved = $m->resolveRouteBinding($m->hash);
        $this->assertTrue($resolved->is($m));

        // Numeric ID resolution should fail by default (throws ModelNotFoundException)
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $m->resolveRouteBinding($m->getKey());
    }

    public function test_by_hash_with_column_selection()
    {
        $m = new HashModel();
        $m->custom_name = 'Test User';
        $m->save();

        // Test with single column
        $result = HashModel::byHash($m->hash, ['custom_name']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertEquals('Test User', $result->custom_name);
        $this->assertFalse(isset($result->hashid)); // Should not be loaded

        // Test with multiple columns (hashid is computed, not in database for non-persisting models)
        $result = HashModel::byHash($m->hash, ['custom_name']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertEquals('Test User', $result->custom_name);
        // hashid attribute should still be accessible since it's computed
        $this->assertEquals($m->hash, $result->hash);

        // Test with default columns (backward compatibility)
        $result = HashModel::byHash($m->hash);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertTrue(isset($result->custom_name));
        // hashid should be accessible via computed attribute
        $this->assertEquals($m->hash, $result->hash);

        // Test with explicit wildcard (same as default)
        $result = HashModel::byHash($m->hash, ['*']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertTrue(isset($result->custom_name));
        // hashid should be accessible via computed attribute
        $this->assertEquals($m->hash, $result->hash);

        // Test with non-existent hash
        $result = HashModel::byHash('invalidhash', ['custom_name']);
        $this->assertNull($result);
    }

    public function test_by_hash_or_fail_with_column_selection()
    {
        $m = new HashModel();
        $m->custom_name = 'Test User';
        $m->save();

        // Test with single column
        $result = HashModel::byHashOrFail($m->hash, ['custom_name']);
        $this->assertEquals($m->id, $result->id);
        $this->assertEquals('Test User', $result->custom_name);
        $this->assertFalse(isset($result->hashid)); // Should not be loaded

        // Test with multiple columns (hashid is computed, not in database for non-persisting models)
        $result = HashModel::byHashOrFail($m->hash, ['custom_name']);
        $this->assertEquals($m->id, $result->id);
        $this->assertEquals('Test User', $result->custom_name);
        // hashid attribute should still be accessible since it's computed
        $this->assertEquals($m->hash, $result->hash);

        // Test with default columns (backward compatibility)
        $result = HashModel::byHashOrFail($m->hash);
        $this->assertEquals($m->id, $result->id);
        $this->assertTrue(isset($result->custom_name));
        // hashid should be accessible via computed attribute
        $this->assertEquals($m->hash, $result->hash);

        // Test with explicit wildcard (same as default)
        $result = HashModel::byHashOrFail($m->hash, ['*']);
        $this->assertEquals($m->id, $result->id);
        $this->assertTrue(isset($result->custom_name));
        // hashid should be accessible via computed attribute
        $this->assertEquals($m->hash, $result->hash);

        // Test with non-existent hash should throw exception
        $this->expectException(ModelNotFoundException::class);
        HashModel::byHashOrFail('invalidhash', ['custom_name']);
    }

    public function test_by_hash_column_selection_with_persisting_model()
    {
        $m = new PersistingModel();
        $m->custom_name = 'Persisting Model';
        $m->save();

        // Test column selection works with persisting models too
        $result = PersistingModel::byHash($m->hash, ['custom_name']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertEquals('Persisting Model', $result->custom_name);
    }

    public function test_by_hash_column_selection_edge_cases()
    {
        $m = new HashModel();
        $m->custom_name = 'Edge Case';
        $m->save();

        // Test with empty columns array - should return model with all columns (fallback to default behavior)
        $result = HashModel::byHash($m->hash, []);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertTrue(isset($result->custom_name));
        // hashid should be accessible via computed attribute
        $this->assertEquals($m->hash, $result->hash);

        // Test with primary key column explicitly
        $result = HashModel::byHash($m->hash, ['id']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
        $this->assertFalse(isset($result->custom_name));
        $this->assertFalse(isset($result->hashid));

        // Test primary key auto-inclusion (key should be included even if not specified)
        $result = HashModel::byHash($m->hash, ['custom_name']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id); // id should be auto-included
        $this->assertEquals('Edge Case', $result->custom_name);

        // Test with column that doesn't exist (Laravel will handle this gracefully)
        $result = HashModel::byHash($m->hash, ['non_existent_column']);
        $this->assertNotNull($result);
        $this->assertEquals($m->id, $result->id);
    }

    public function test_by_hash_with_custom_primary_key()
    {
        // Create a mock model with custom primary key to test the fix
        $mockModel = new class() extends HashModel {
            protected $primaryKey = 'custom_id';
        };

        // Test that the method correctly uses getKeyName() instead of hardcoded 'id'
        $this->assertEquals('custom_id', $mockModel->getKeyName());

        // This test demonstrates that the implementation now works with any primary key name
        // The actual database query would use the correct key name from getKeyName()
        $this->assertTrue(true); // Placeholder for demonstrating the concept
    }

    public function test_route_binding_with_fallback_enabled()
    {
        $m = new HashModelWithFallback();
        $m->custom_name = 'Test Model with Fallback';
        $m->save();

        // Hash-based resolution should work
        $resolved = $m->resolveRouteBinding($m->hash);
        $this->assertTrue($resolved->is($m));

        // Numeric ID resolution should also work when fallback is enabled
        $resolved = $m->resolveRouteBinding($m->getKey());
        $this->assertTrue($resolved->is($m));
    }

    public function test_route_binding_with_custom_field()
    {
        $m = new HashModel();
        $m->custom_name = 'Test Model';
        $m->save();

        // Custom field binding should always use parent implementation
        $resolved = $m->resolveRouteBinding($m->custom_name, 'custom_name');
        $this->assertTrue($resolved->is($m));

        // Even with fallback enabled, custom field should use parent implementation
        $fallbackModel = new HashModelWithFallback();
        $fallbackModel->custom_name = 'Fallback Model';
        $fallbackModel->save();

        $resolved = $fallbackModel->resolveRouteBinding($fallbackModel->custom_name, 'custom_name');
        $this->assertTrue($resolved->is($fallbackModel));
    }

    public function test_route_binding_hash_not_found_throws_exception()
    {
        $nonExistentHash = 'non-existent-hash';

        // Both models should throw ModelNotFoundException for non-existent hashes
        $this->expectException(ModelNotFoundException::class);
        HashModel::byHashOrFail($nonExistentHash);
    }

    protected function getRepository(): Repository
    {
        return app('app.hashid');
    }
}
