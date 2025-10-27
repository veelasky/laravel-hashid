# GitHub CI/CD Workflow Upgrade

## Overview

This document outlines the changes made to the GitHub Actions workflows to support PHP 8.4 and Laravel 11/12 compatibility testing.

## Changes Made

### 1. Main Test Workflow (`.github/workflows/test.yml`)

**Updated Matrix Configuration:**
- **PHP Versions**: Added PHP 8.4 to the existing [8.1, 8.2, 8.3] matrix
- **Laravel Versions**: Added Laravel 11.* to existing 10.* matrix
- **Smart Combinations**: Added explicit include/exclude rules for compatible combinations

**Modernization Updates:**
- Upgraded from `ubuntu-20.04` to `ubuntu-latest`
- Updated actions to latest versions:
  - `actions/checkout@v1` → `actions/checkout@v4`
  - `actions/cache@v2` → `actions/cache@v4`
  - `codecov/codecov-action@v1` → `codecov/codecov-action@v4`
- Fixed deprecated GitHub Actions syntax (`::set-output` → `$GITHUB_OUTPUT`)
- Added `fail-fast: false` to allow other jobs to continue if one fails
- Added composer validation step

**Test Matrix:**
```yaml
# Base matrix
php: [8.1, 8.2, 8.3, 8.4]
laravel: [10.*, 11.*]

# Explicit combinations with testbench versions
include:
  - php: 8.4, laravel: 11.*, testbench: ^9.0
  - php: 8.3, laravel: 11.*, testbench: ^9.0
  - php: 8.2, laravel: 11.*, testbench: ^9.0
  - php: 8.4, laravel: 10.*, testbench: ^8.0|^9.0
  - php: 8.3, laravel: 10.*, testbench: ^8.0|^9.0
  - php: 8.2, laravel: 10.*, testbench: ^8.0|^9.0
  - php: 8.1, laravel: 10.*, testbench: ^8.0

# Exclude incompatible combinations
exclude:
  - php: 8.1, laravel: 11.*  # Laravel 11 requires PHP 8.2+
```

### 2. Future Compatibility Workflow (`.github/workflows/laravel-future.yml`)

**Purpose:** Test against Laravel 12 (dev-master) before it's officially released.

**Features:**
- Tests PHP [8.2, 8.3, 8.4] against Laravel dev-master
- Runs on schedule (daily) and on pushes/PRs
- Continues on error since this is testing unreleased versions
- Uses `--ignore-platform-reqs` for development dependencies

### 3. Compatibility Matrix Workflow (`.github/workflows/compatibility-matrix.yml`)

**Purpose:** Lightweight testing for the experiment branch to validate matrix combinations.

**Features:**
- Tests key combinations without full coverage
- Faster feedback for development
- Includes dry-run dependency checks
- Clear success/failure reporting

## Testing Strategy

### Supported Combinations

| PHP Version | Laravel 10 | Laravel 11 | Laravel 12 (future) |
|-------------|------------|------------|-------------------|
| 8.1         | ✅         | ❌         | ❌                |
| 8.2         | ✅         | ✅         | ✅                |
| 8.3         | ✅         | ✅         | ✅                |
| 8.4         | ✅         | ✅         | ✅                |

### Dependency Management

The workflows handle different dependency requirements:

1. **Laravel 10**: Uses Orchestra Testbench ^8.0|^9.0
2. **Laravel 11**: Uses Orchestra Testbench ^9.0
3. **Laravel 12**: Uses Orchestra Testbench dev-master

### Benefits

1. **Comprehensive Testing**: Tests all supported PHP/Laravel combinations
2. **Future-Proofing**: Automatically tests against upcoming Laravel versions
3. **Fast Feedback**: Parallel execution with smart caching
4. **Reliability**: Continues on error for future versions to avoid blocking releases
5. **Modern Actions**: Uses latest GitHub Actions with best practices

## Usage

The workflows will automatically run when:
- Code is pushed to `master` or `main` branches
- Pull requests are created
- Daily schedule runs (for future compatibility testing)

The main test workflow provides coverage reports to Codecov, while the future workflow provides early warning about compatibility issues with upcoming Laravel releases.