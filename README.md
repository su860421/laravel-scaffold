# 🚀 Laravel Scaffold - Enterprise-Grade CRUD Generator

> **From 0 to Complete API in 30 Seconds** - Auto-generate Repository, Service, Controller with Laravel best practices

[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-12.x%20%7C%2013.x-green.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Tests](https://github.com/su860421/laravel-scaffold/actions/workflows/tests.yml/badge.svg)](https://github.com/su860421/laravel-scaffold/actions/workflows/tests.yml)

**Solve the Problem**: Eliminate repetitive CRUD code and implement enterprise-grade architecture patterns in Laravel development.

## 🎯 Why Choose Laravel Scaffold?

| Feature | Laravel Scaffold | Other Packages |
|---------|------------------|----------------|
| **Repository Pattern** | ✅ Complete implementation | ❌ Basic only |
| **Service Layer** | ✅ Business logic separation | ❌ Missing |
| **Auto Setup** | ✅ Routes & bindings | ❌ Manual setup |
| **Multilingual Support** | ✅ Built-in EN/CN | ❌ English only |【
| **Advanced Features** | ✅ Filtering, sorting, batch ops | ❌ Limited |
| **Clean Architecture** | ✅ Interface contracts | ❌ Direct coupling |

## 🚀 Quick Start

```bash
# Install the package
composer require joesu/laravel-scaffold

# Generate complete CRUD architecture
php artisan make:repository User

# That's it! Your API is ready to use
```

**30 seconds later, you have a complete User API with enterprise architecture!**

## 🎯 Perfect For

- ✅ **Enterprise API Development** - Production-ready architecture
- ✅ **SaaS Backend Systems** - Scalable service layer
- ✅ **Rapid Prototyping** - Quick MVP development
- ✅ **Team Collaboration** - Consistent code patterns
- ✅ **Code Quality** - Clean architecture patterns

## 🏗️ Architecture

Laravel Scaffold provides a complete layered architecture with enterprise-grade patterns:

### Base Classes
- **BaseRepository**: Full CRUD implementation with advanced features
- **BaseService**: Business logic layer with batch operations

### Generated Structure
When you run `php artisan make:repository User`, you get:

```
app/
├── Models/
│   └── User.php                    # Eloquent Model
├── Http/
│   ├── Controllers/
│   │   └── UserController.php      # API Controller
│   └── Requests/
│       └── User/                   # Request validation classes
│           ├── StoreUserRequest.php    # Store validation
│           ├── UpdateUserRequest.php   # Update validation
│           ├── IndexUserRequest.php    # Index validation
│           └── ShowUserRequest.php     # Show validation
├── Repositories/
│   └── UserRepository.php          # Extends BaseRepository
├── Services/
│   └── UserService.php             # Extends BaseService
└── Contracts/
    ├── UserRepositoryInterface.php # Repository contract
    └── UserServiceInterface.php    # Service contract
```

**Automatic Setup:**
- ✅ **Service Provider Bindings**: Automatically added to `AppServiceProvider`
- ✅ **API Routes**: Automatically added to `routes/api.php`
- ✅ **Dependency Injection**: Ready to use with Laravel's DI container

## ✨ Key Features

### 🔄 Advanced Repository Features
- **Complete CRUD operations** with error handling
- **Advanced query methods** with relationship loading
- **Smart filtering system** (JSON and array formats)
- **Relationship field filtering** (e.g., 'user.name')
- **Configurable sorting** with whitelist protection
- **Batch operations** (create, update, delete)
- **Soft delete support** with restore functionality
- **Multilingual error messages** (EN/CN)

### 🎛️ Service Layer Features
- **Business logic encapsulation**
- **Batch operations** with transaction support
- **Utility methods** (updateOrCreate, exists, count)
- **Soft delete operations**
- **Error handling** with custom exceptions

## 🛠️ Installation & Setup

### 1. Install Package
```bash
composer require joesu/laravel-scaffold
```

### 2. Publish Configuration (Optional)
```bash
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider"
```

### 3. Generate Your First CRUD
```bash
# Complete CRUD generation with automatic setup
php artisan make:repository User

# Include migration file
php artisan make:repository User --migration

# Include request validation classes
php artisan make:repository User --requests

# Include both migration and requests
php artisan make:repository User --migration --requests

# Force overwrite existing files
php artisan make:repository User --force
```

**What happens automatically:**
- ✅ Model is always created (needed for Repository and Service)
- ✅ Service provider bindings are added to `AppServiceProvider`
- ✅ API routes are added to `routes/api.php`
- ✅ All files are properly namespaced and ready to use
- ✅ No manual configuration required

**Available Options:**
- `--migration`: Create migration file
- `--requests`: Create request validation classes
- `--force`: Overwrite existing files

## 🌍 Multilingual Support

Built-in support for multiple languages with automatic error message translation:

### Supported Languages
- 🇺🇸 English (en)
- 🇹🇼 Chinese Traditional (zh-TW)

### Publishing Language Files
```bash
# Publish all language files
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider" --tag=lang
```

### Error Message Examples
```php
// English
"Record with ID 123 not found"
"Failed to create model: SQLSTATE[23000]: Integrity constraint violation"

// Chinese Traditional (when locale is zh-TW)
"找不到 ID 為 123 的記錄"
"建立模型失敗: SQLSTATE[23000]: Integrity constraint violation"
```

## 🔧 Configuration

The package automatically registers service providers and bindings. For custom configuration:

```bash
# Publish config file
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider"
```

## 🧪 Testing

```bash
# Run package tests
composer test

# Run with coverage
composer test-coverage
```

## 📋 Requirements

- **PHP**: ^8.3 (8.3, 8.4, 8.5)
- **Laravel**: ^12.0|^13.0

## 🔄 Version Compatibility

| Laravel Version | PHP Version | Status |
|----------------|-------------|---------|
| 13.x | 8.3 – 8.5 | ✅ Supported |
| 12.x | 8.3 – 8.5 | ✅ Supported |
| 10.x / 11.x | 8.1 – 8.4 | Use `joesu/laravel-scaffold:^1.1` |

For detailed version compatibility, see [VERSION_COMPATIBILITY.md](VERSION_COMPATIBILITY.md).

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

This package is open-sourced software licensed under the [MIT License](LICENSE).

## 🆘 Support

- 📧 **Issues**: [GitHub Issues](https://github.com/su860421/laravel-scaffold/issues)
- 📚 **Documentation**: [GitHub Wiki](https://github.com/su860421/laravel-scaffold/wiki)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/su860421/laravel-scaffold/discussions)

## ⭐ Show Your Support

If this package helps you, please give it a ⭐ on GitHub!

---

**Built with ❤️ for the Laravel community** 