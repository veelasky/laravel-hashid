# Laravel HashId
![Test](https://github.com/veelasky/laravel-hashid/workflows/Test/badge.svg)
[![codecov](https://codecov.io/gh/veelasky/laravel-hashid/branch/master/graph/badge.svg?token=t95ymsMyDX)](https://codecov.io/gh/veelasky/laravel-hashid)
[![Latest Stable Version](https://poser.pugx.org/veelasky/laravel-hashid/v)](//packagist.org/packages/veelasky/laravel-hashid)
[![Total Downloads](https://poser.pugx.org/veelasky/laravel-hashid/downloads)](//packagist.org/packages/veelasky/laravel-hashid)
[![composer.lock](https://poser.pugx.org/veelasky/laravel-hashid/composerlock)](//packagist.org/packages/veelasky/laravel-hashid)
[![Dependents](https://poser.pugx.org/veelasky/laravel-hashid/dependents)](//packagist.org/packages/veelasky/laravel-hashid)
[![License](https://poser.pugx.org/veelasky/laravel-hashid/license)](//packagist.org/packages/veelasky/laravel-hashid)

Automatic HashId generator for your eloquent model.

### Install

```
composer require veelasky/laravel-hashid
```

With laravel package auto discovery, this will automatically add this package to your laravel application.

### TLDR;

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
$user = User::byHash($hash);         // get user by hashed id.
$user = User::byHashOrFail($hash);   // get user by hashed id, and throw ModelNotFoundException if not present.
User::idToHash($id);                 // get hashed id from the primary key.
User::hashToId($hash);               // get ID from hashed string.
User::query()->byHash($hash);        // query scope with `byHash` method.
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

You can also specify the length and characters of the hashed Id with `HASHID_LENGTH` and `HASHID_ALPHABET` environment variable respectively, or you can publish the configuration file using this command:

```bash
php artisan vendor:publish --tags=laravel-hashid-config
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
