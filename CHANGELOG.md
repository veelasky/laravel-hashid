# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [3.2.0] - 2025-10-27

### Added
- Laravel 11 compatibility
- Laravel 12 compatibility support (future-proofing)
- PHPUnit 11 compatibility
- Support for PHP 8.4 (while maintaining backward compatibility)
- Updated Orchestra Testbench to support both v8 and v9

### Changed
- Updated dependency constraints to be more flexible:
  - `phpunit/phpunit`: `^10.0|^11.0` (was `>=10.0`)
  - `orchestra/testbench`: `^8.0|^9.0` (was `>=8.0`)
- Updated branch alias from `4.0.x-dev` to `3.2.x-dev` for proper versioning

### Compatibility
- **PHP**: `^8.1` (supports 8.1, 8.2, 8.3, and 8.4)
- **Laravel**: `>=10.0` (supports Laravel 10, 11, and future 12 compatibility)
- **PHPUnit**: `^10.0|^11.0`
- **Orchestra Testbench**: `^8.0|^9.0`

### Notes
- All tests pass with both PHPUnit 10 and 11
- Backward compatibility fully maintained with existing Laravel 10 installations
- Ready for Laravel 12 when released (scheduled for February 2025)

## [3.1.x] - Previous Versions

### Supported Versions
- Laravel 6.x, 7.x, 8.x, 9.x, 10.x
- PHP `>= 8.0`

For detailed changes in previous versions, please refer to the git commit history.