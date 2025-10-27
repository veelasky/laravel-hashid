## Laravel HashId
![CI/CD Pipeline](https://github.com/veelasky/laravel-hashid/workflows/CI%2FCD%20Pipeline/badge.svg)
![🔒 Security Scanning](https://github.com/veelasky/laravel-hashid/workflows/%F0%9F%94%92%20Security%20Scanning/badge.svg)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3e929b5327a9453bb0da5cbf2ecb8794)](https://app.codacy.com/gh/veelasky/laravel-hashid?utm_source=github.com&utm_medium=referral&utm_content=veelasky/laravel-hashid&utm_campaign=Badge_Grade)
[![codecov](https://codecov.io/gh/veelasky/laravel-hashid/branch/master/graph/badge.svg?token=t95ymsMyDX)](https://codecov.io/gh/veelasky/laravel-hashid)
[![Latest Stable Version](https://poser.pugx.org/veelasky/laravel-hashid/v)](//packagist.org/packages/veelasky/laravel-hashid)
[![StyleCI](https://github.styleci.io/repos/118424643/shield?branch=master)](https://github.styleci.io/repos/118424643?branch=master)
[![Total Downloads](https://poser.pugx.org/veelasky/laravel-hashid/downloads)](//packagist.org/packages/veelasky/laravel-hashid)
[![Dependents](https://poser.pugx.org/veelasky/laravel-hashid/dependents)](//packagist.org/packages/veelasky/laravel-hashid)
[![License](https://poser.pugx.org/veelasky/laravel-hashid/license)](//packagist.org/packages/veelasky/laravel-hashid)

Automatic HashId generator for your eloquent model.

### 🎉 Version 4.x Development & v3.2.0 Released!

**Version 3.2.0** brings **Laravel 11/12 compatibility** and **PHP 8.4 support** with enhanced column selection features.

**Version 4.x** is the current development branch with continued improvements.

**Key Updates:**
- ✅ Laravel 11 & 12 compatibility
- ✅ PHP 8.4 support (ready for the future)
- ✅ PHPUnit 10 & 11 compatibility
- ✅ **New Column Selection** for `byHash()` and `byHashOrFail()` methods
- ✅ Enhanced performance and type safety
- ✅ Maintained backward compatibility

See the [CHANGELOG](CHANGELOG.md) for detailed release notes.

### Version Compatibilities

| Laravel HashId 	 |   PHP Version      	    |     Laravel 6.*    	|     Laravel 7.*    	|     Laravel 8.*    	|     Laravel 9.*    	|     Laravel 10.*    	|     Laravel 11.*    	|     Laravel 12.*    	|
|------------------|:-----------------------:|:------------------:	|:------------------:	|:------------------:	|:------------------:	|:------------------:	|:------------------:	|:------------------:	|
| `1.x`     	      | `>=7.0`               	 | ✅ 	| ❌                	| ❌                	| ❌                	| ❌                	| ❌                	| ❌                	|
| `2.x`     	      | `>=7.2` - `<= 8.0`    	 | ✅ 	| ✅ 	| ✅ 	| ✅ 	| ❌ 	| ❌                	| ❌                	|
| `3.0`     	      | `>=7.4` \|\| `>= 8.0` 	 | ✅ 	| ✅ 	| ✅ 	| ✅ 	| ❌ 	| ❌                	| ❌                	|
| `3.1`     	      |       `>= 8.0` 	        | ✅ 	| ✅ 	| ✅ 	| ✅ 	| ✅ 	| ❌                	| ❌                	|
| `3.2`     	      |       `>= 8.1` 🌟        | ❌                	| ❌ 	| ❌ 	| ❌ 	| ✅ 	| ✅ 	| ✅ 	|
| `4.x`     	      |       `>= 8.1` 🚀        | ❌                	| ❌ 	| ❌ 	| ❌ 	| ✅ 	| ✅ 	| ✅ 	|

**🌟 Stable Release (3.2)** - Recommended version with full modern support and column selection features
**🚀 Development Branch (4.x)** - Current development with latest improvements
**📋 PHP 8.4 Ready** - Compatible with the latest PHP features
**🔮 Laravel 12 Ready** - Future-proofed for upcoming Laravel releases

### Install

```bash
composer require veelasky/laravel-hashid
```

With laravel package auto discovery, this will automatically add this package to your laravel application.

### TLDR

Simply add `HashableId` trait on any of your eloquent model you are intending to use with HashId.

Example:
```php
use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class User extends Model {
    use HashableId;
    ...
}
```

### Usage

#### With Eloquent Model
```php

$user = User::find(1);     // instance of user.
$user->hash;               // generate HashId.

// Database operation

// get user by hashed id.
$user = User::byHash($hash);

// get user by hashed id with specific columns (NEW FEATURE!).
$user = User::byHash($hash, ['name', 'email']);

// get user by hashed id with single column (NEW FEATURE!).
$user = User::byHash($hash, ['name']);

// get user by hashed id, and throw ModelNotFoundException if not present.
$user = User::byHashOrFail($hash);

// get user by hashed id with specific columns and fail if not found (NEW FEATURE!).
$user = User::byHashOrFail($hash, ['name', 'email']);

// get hashed id from the primary key.
User::idToHash($id);

// get ID from hashed string.
User::hashToId($hash);

// query scope with `byHash` method.
User::query()->byHash($hash);

// 🚀 New Column Selection Feature (v3.2+):
// - Better performance: Only loads the columns you need
// - Type safety: Automatic primary key inclusion when needed
// - Backward compatible: All existing code continues to work unchanged
// - Smart defaults: ['*'] loads all columns, just like before
```

By default, all hash calculation will be calculated at runtime, but sometime you want to persist the hashed id to the database.

> NOTE: when using persisting model, all database query will be check againts the table itself, except: `$model->hash` will always be calculated at runtime.
```php
class User extends Model {
    use HashableId;

    // add this property to your model if you want to persist to the database.
    protected $shouldHashPersist = true;

    // by default, the persisted value will be stored in `hashid` column
    // override column name to your desired name.
    protected $hashColumnName = 'hashid';
    ...
}

```

#### Salt

The salt is generated automatically based on your app key and hash_alphabet. If you need to use the same salt between different projects, you can set the `HASHID_SALT` environment variable.

#### Route binding

When HashableId trait is used, base `getRouteKey()` and `resolveRouteBinding()` are overwritten to use the HashId as route key.

```php
use App\Models\User;

class UserController extends Controller
{
    /**
     * Route /users/{user}
     * Ex: GET /users/k1jTdv6l
     */
    public function show(User $user)
    {
        ...
    }
}
```

#### In-Depth Coverage

This package use repository pattern to store all instantiated implementation of `HashId\HashId` class, this to achieve different hash result on every eloquent models defined with `HashableId` trait.

```php
// using facade.
HashId::hashToId($hash, User::class)      // same as User::hashToId($hash);
HashId::idToHash($id, User::class)        // same as User::idToHash($hash);

// HashId facade class is an implementation of \Veelasky\Laravel\HashId\Repository
```

However you can opt-out to not using any eloquent model or implementing your own logic to the repository.

```php
HashId::make($key, $salt);              // will return \HashId\HashId class.

// once you instantiated the object, you can retrieve it on your next operation
HashId::get($key);
```

If you're using single table inheritance model, where you want to has the same calculated hash across all inherited models, use `$hashKey` property, this will result the calculation remain the same across all inherited model.

```php
class User extends Model {
    protected $hashKey = 'somethingUnique';
}

class Customer extends User {

}

$customer = Customer::find(1);
$user = User::find(1);

$user->hash; // will be equal to $customer->hash
```

You can also specify the length and characters of the hashed Id with `HASHID_LENGTH` and `HASHID_ALPHABET` environment variable respectively, or you can publish the configuration file using this command:

```bash
php artisan vendor:publish --tag=laravel-hashid-config
```

#### Extra: Validation Rules

You can also use this as validation rules, simply add this rule to your validator.

```php
use App\Models\User;
use Veelasky\LaravelHashId\Rules\ExistsByHash;

...
Validator::make([
    'id' => $hashedId
], [
    'id' => ['required', new ExistsByHash(User::class)],
]);
...
```

#### License

MIT License
