# Laravel HashId

![CI/CD Pipeline](https://github.com/veelasky/laravel-hashid/workflows/CI%2FCD%20Pipeline/badge.svg)
![🔒 Security Scanning](https://github.com/veelasky/laravel-hashid/workflows/%F0%9F%94%92%20Security%20Scanning/badge.svg)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/3e929b5327a9453bb0da5cbf2ecb8794)](https://app.codacy.com/gh/veelasky/laravel-hashid?utm_source=github.com&utm_medium=referral&utm_content=veelasky/laravel-hashid&utm_campaign=Badge_Grade)
[![codecov](https://codecov.io/gh/veelasky/laravel-hashid/branch/master/graph/badge.svg?token=t95ymsMyDX)](https://codecov.io/gh/veelasky/laravel-hashid)
[![Latest Stable Version](https://poser.pugx.org/veelasky/laravel-hashid/v)](//packagist.org/packages/veelasky/laravel-hashid)
[![StyleCI](https://github.styleci.io/repos/118424643/shield?branch=master)](https://github.styleci.io/repos/118424643?branch=master)
[![Total Downloads](https://poser.pugx.org/veelasky/laravel-hashid/downloads)](//packagist.org/packages/veelasky/laravel-hashid)
[![Dependents](https://poser.pugx.org/veelasky/laravel-hashid/dependents)](//packagist.org/packages/veelasky/laravel-hashid)
[![License](https://poser.pugx.org/veelasky/laravel-hashid/license)](//packagist.org/packages/veelasky/laravel-hashid)

> Automatic HashId generator for your Laravel Eloquent models.

## About

Laravel HashId provides an elegant way to add hashed IDs to your Eloquent models. It generates unique, non-sequential hashes for your model IDs and provides convenient methods to work with them.

## ✨ Latest Features

**Version 3.2.2** introduces enhanced secure route model binding along with powerful column selection capabilities and full Laravel 11/12 and PHP 8.4 compatibility.

### 🔒 Secure Route Model Binding (New in v3.2.2)

**Security improvement:** Route model binding now only accepts valid hash values by default, preventing predictable ID enumeration attacks:

```php
class User extends Model
{
    use HashableId;
    // Default: only hash resolution, numeric IDs return 404
}

// ✅ Secure: /users/k1jTdv6l works
// ❌ Blocked: /users/1 returns 404 (prevents ID enumeration)
```

### 🔥 Column Selection API (New in v3.2.0)

```php
// Get user by hash with specific columns (better performance!)
$user = User::byHash($hash, ['name', 'email']);

// Get user by hash with single column
$user = User::byHash($hash, ['name']);

// Column selection with exception handling
$user = User::byHashOrFail($hash, ['name', 'email']);
```

**Benefits:**
- 🚀 **Better Performance** - Load only the columns you need
- 🔒 **Type Safety** - Automatic primary key inclusion when required
- 🔄 **Backward Compatible** - All existing code works unchanged
- 🎯 **Smart Defaults** - `['*']` loads all columns, just like before
- 🛡️ **Enhanced Security** - Prevents ID enumeration attacks by default

## Compatibility

### Modern Laravel Support (Recommended)

| Laravel HashId | PHP Version | Laravel 10 | Laravel 11 | Laravel 12 |
|----------------|-------------|-------------|-------------|-------------|
| **3.2** 🌟      | **≥ 8.1**   | ✅          | ✅          | ✅          |
| **4.x** 🚀      | **≥ 8.1**   | ✅          | ✅          | ✅          |

- 🌟 **Stable Release (3.2)** - Recommended for production
- 🚀 **Development Branch (4.x)** - Latest improvements

### Full Version Matrix

| Laravel HashId | PHP Version | Laravel 6 | Laravel 7 | Laravel 8 | Laravel 9 | Laravel 10 | Laravel 11 | Laravel 12 |
|----------------|-------------|-----------|-----------|-----------|-----------|-------------|-------------|-------------|
| **1.x**         | `≥ 7.0`   | ✅         | ❌         | ❌         | ❌         | ❌         | ❌         | ❌         |
| **2.x**         | `≥ 7.2`   | ❌         | ✅         | ✅         | ✅         | ❌         | ❌         | ❌         |
| **3.0**         | `≥ 7.4`   | ❌         | ✅         | ✅         | ✅         | ❌         | ❌         | ❌         |
| **3.1**         | `≥ 8.0`   | ❌         | ✅         | ✅         | ✅         | ✅         | ❌         | ❌         |
| **3.2** 🌟      | `≥ 8.1`   | ❌         | ❌         | ❌         | ❌         | ✅         | ✅         | ✅         |
| **4.x** 🚀      | `≥ 8.1`   | ❌         | ❌         | ❌         | ❌         | ✅         | ✅         | ✅         |

**📊 Version Recommendations:**
- **Laravel 6-9** → Use `3.0` or `3.1`
- **Laravel 10+** → Use `3.2` (stable) or `4.x` (development)
- **Latest features** → Use `3.2+` with column selection support

## Installation

```bash
composer require veelasky/laravel-hashid
```

With Laravel's package auto-discovery, the package will be automatically registered.

## Quick Start

Simply add the `HashableId` trait to any Eloquent model you want to use with HashId:

```php
use Illuminate\Database\Eloquent\Model;
use Veelasky\LaravelHashId\Eloquent\HashableId;

class User extends Model
{
    use HashableId;
}
```

## Usage Examples

### Basic Usage

```php
$user = User::find(1);           // Find user by ID
$user->hash;                     // Get HashId automatically

// Find by HashId
$user = User::byHash($hash);
$user = User::byHashOrFail($hash); // Throws exception if not found

// Convert between ID and HashId
$hashedId = User::idToHash($id);
$originalId = User::hashToId($hash);

// Query scope
User::query()->byHash($hash)->get();
```

### Column Selection (New in v3.2)

```php
// Load only specific columns for better performance
$user = User::byHash($hash, ['name', 'email']);

// Single column selection
$user = User::byHash($hash, ['name']);

// Column selection with exception handling
$user = User::byHashOrFail($hash, ['name', 'email']);
```

### Persisting HashId to Database

```php
class User extends Model
{
    use HashableId;

    protected $shouldHashPersist = true;  // Persist hash to database
    protected $hashColumnName = 'hashid';  // Custom column name (optional)
}
```

### Route Model Binding

The trait automatically overwrites route methods to use HashId:

```php
Route::get('/users/{user}', [UserController::class, 'show']);

class UserController
{
    public function show(User $user)
    {
        // $user resolves automatically by HashId
        // Example URL: /users/k1jTdv6l
        // Numeric IDs like /users/1 will return 404 by default (secure!)
    }
}
```

#### 🔒 Secure Route Model Binding (Default)

By default, route model binding only accepts valid hash values and returns 404 for plain numeric IDs, preventing predictable ID enumeration attacks:

```php
class User extends Model
{
    use HashableId;
    // $bindingFallback = false; // Default behavior - only hash resolution
}

// ✅ This works: /users/k1jTdv6l
// ❌ This returns 404: /users/1 (prevents ID enumeration)
```

#### Optional Fallback to Numeric ID

If you need to support both hash and numeric ID resolution (not recommended for production), you can enable the fallback:

```php
class User extends Model
{
    use HashableId;

    protected $bindingFallback = true; // Allow both hash and numeric ID resolution
}

// ✅ Both work: /users/k1jTdv6l AND /users/1
```

#### Custom Field Binding

Custom field binding always uses Laravel's default behavior:

```php
Route::get('/users/{user:slug}', [UserController::class, 'show']);

// This will resolve by 'slug' field, not by hash
```

### Validation Rules

```php
use App\Models\User;
use Veelasky\LaravelHashId\Rules\ExistsByHash;

$request->validate([
    'user_id' => ['required', new ExistsByHash(User::class)],
]);
```

### Advanced Usage

#### Repository Pattern Access

```php
// Using the HashId facade
$hashedId = HashId::idToHash($id, User::class);
$originalId = HashId::hashToId($hash, User::class);

// Manual hash ID creation
$hashId = HashId::make('custom-key', 'custom-salt');
```

#### Shared Hash Across Models

```php
class User extends Model
{
    protected $hashKey = 'shared-hash-key';
}

class Customer extends User { }

$customer = Customer::find(1);
$user = User::find(1);

// Both will have the same hash
echo $customer->hash === $user->hash; // true
```

## Configuration

You can configure HashId behavior using environment variables:

```env
HASHID_SALT=your-custom-salt
HASHID_LENGTH=10
HASHID_ALPHABET=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890
```

Or publish the configuration file:

```bash
php artisan vendor:publish --tag=laravel-hashid-config
```

## License

MIT License. Feel free to use this package in your projects!