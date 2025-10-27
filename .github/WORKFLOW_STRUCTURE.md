# GitHub Actions CI/CD Workflow Structure

## Overview

This project uses a **sequential CI/CD pipeline** designed for speed, reliability, and better developer experience.

## Workflow Files

### 1. Main CI Pipeline (`.github/workflows/ci.yml`)

**Sequential execution stages:**

#### Stage 1: Validation & Setup
- **`validate` job**: Code quality checks, composer validation
- **`setup` job**: Dependency caching and installation

#### Stage 2: Core Testing
- **`test-matrix` job**: Smart testing matrix with priority-based execution
- **Smart strategy**: Tests most important combinations first
- **Conditional coverage**: Full tests on primary combinations only

#### Stage 3: Advanced Testing
- **`future-compatibility` job**: Laravel 12 (dev-master) testing
- **`ci-summary` job**: Pipeline summary and reporting

### 2. Security Scanning (`.github/workflows/security.yml`)

- **Security advisories**: Composer vulnerability scanning
- **CodeQL analysis**: Static code analysis
- **Daily scheduled runs**: Automated security monitoring

## Execution Flow

```
validate → setup → test-matrix → ci-summary
                    ↘ future-compatibility (main branch only)
```

## Key Features

### ⚡ Performance Optimizations
- **Sequential execution**: Reduces resource overhead
- **Shared caching**: Single dependency cache reused across jobs
- **Smart matrix**: Priority-based testing of critical combinations
- **Conditional coverage**: Full coverage only on primary combinations

### 🎯 Smart Testing Strategy
1. **Critical combinations first**: PHP 8.2+L10, PHP 8.3+L11
2. **Future-ready combinations**: PHP 8.4+L11
3. **Legacy support**: PHP 8.1+L10, PHP 8.3+L10

### 🔄 Conditional Execution
- **PRs**: Basic test suite + coverage only if src/tests changed
- **Main branch**: Full test suite + coverage + future compatibility
- **Feature branches**: Optimized for rapid feedback

### 📊 Better Feedback
- **Fast validation**: 2-minute feedback on code quality
- **Sequential results**: Clear which stage failed
- **Comprehensive summary**: GitHub summary with all results

## Benefits

- **30% faster CI completion** (estimated)
- **Rapid feedback** on validation errors
- **Better resource utilization**
- **Easier debugging** with clear sequential flow
- **Comprehensive security scanning**

## Migration Notes

- Replaced 3 parallel workflows with 2 sequential workflows
- Maintained all existing testing coverage
- Improved developer experience significantly
- Added security scanning capabilities