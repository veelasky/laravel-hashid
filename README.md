# Laravel HashId
---

Automatic HashId generator for your eloquent model.


### Install

```
composer require veelasky/laravel-hashid
```

With laravel package auto discovery, this will automatically add this package to your laravel application.

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

```php

// instance of user
$user = User::find(1);

// generate HashId
$user->hash;

// querying for user with specific hash
$user = User::byHash($hash); // $hash: insert any hash that you want to check.

// querying for user with specific hash and throw EloquentModelNotFound exception
$user = User::byHashOrFail ($hash)


// convert id to hash from User static instance;
User::idToHash($id);

// convert hash to id from User static instance;
User::hashToId($hash);
```
