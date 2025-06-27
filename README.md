# 🚀 Laravel Scaffold - Enterprise-Grade CRUD Generator

> **From 0 to Complete API in 30 Seconds** - Auto-generate Repository, Service, Controller, Tests, and Smart Cleanup

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x%20%7C%2012.x-green.svg)](https://laravel.com)
[![License](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/Tests-Passing-brightgreen.svg)](https://github.com/joesu/laravel-scaffold)

**Solve the Problem**: Eliminate repetitive CRUD code, improve test coverage, and manage generated files intelligently in Laravel development.

## 🎯 Why Choose Laravel Scaffold?

| Feature | Laravel Scaffold | Other Packages |
|---------|------------------|----------------|
| **Auto-Generated Tests** | ✅ Complete test coverage | ❌ Manual test writing |
| **Smart Cleanup** | ✅ One-click cleanup | ❌ Manual file deletion |
| **Multilingual Support** | ✅ Built-in EN/CN | ❌ English only |
| **Batch Operations** | ✅ Full support | ❌ Partial support |
| **Relationship Filtering** | ✅ Advanced queries | ❌ Basic only |
| **Soft Delete Support** | ✅ Complete implementation | ❌ Limited support |

## 🚀 Quick Start

```bash
# Install the package
composer require joesu/laravel-scaffold

# Generate complete CRUD architecture
php artisan make:repository User

# Run tests (auto-generated!)
php artisan test --filter=UserTest
```

**30 seconds later, you have a complete User API with full test coverage!**

## 🎯 Perfect For

- ✅ **Enterprise API Development** - Production-ready architecture
- ✅ **SaaS Backend Systems** - Scalable service layer
- ✅ **Rapid Prototyping** - Quick MVP development
- ✅ **Team Collaboration** - Consistent code patterns
- ✅ **Code Quality** - Built-in testing and error handling

## 🏗️ Architecture

Laravel Scaffold provides a complete layered architecture with enterprise-grade patterns:

### Base Interfaces
- **BaseRepositoryInterface**: Complete repository contract
- **BaseServiceInterface**: Service layer contract

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
│       ├── StoreUserRequest.php    # Validation rules
│       └── UpdateUserRequest.php   # Update validation
├── Repositories/
│   └── UserRepository.php          # Extends BaseRepository
├── Services/
│   └── UserService.php             # Extends BaseService
├── Tests/
│   ├── Feature/
│   │   └── UserTest.php            # Auto-generated tests
│   └── Unit/
│       ├── UserRepositoryTest.php  # Repository tests
│       └── UserServiceTest.php     # Service tests
└── Contracts/
    ├── UserRepositoryInterface.php # Repository contract
    └── UserServiceInterface.php    # Service contract
```

## ✨ Key Features

### 🧪 Auto-Generated Tests (Unique Feature!)
```bash
# Automatically generates comprehensive tests
php artisan make:repository User

# Run the generated tests
php artisan test --filter=UserTest
```

**Generated test coverage includes:**
- ✅ CRUD operations testing
- ✅ Validation rules testing
- ✅ Error handling testing
- ✅ Batch operations testing
- ✅ Soft delete testing

### 🧹 Smart Cleanup System
```bash
# Clean up generated files when needed
php artisan scaffold:cleanup User

# Clean with confirmation
php artisan scaffold:cleanup User --confirm

# Clean with backup
php artisan scaffold:cleanup User --backup
```

**Safely removes:**
- Generated controllers, repositories, services
- Test files
- Request validation classes
- Interface contracts
- API routes (optional)

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

## 📡 API Usage Examples

### Basic Operations
```bash
# Get all users with pagination
GET /api/users?per_page=10&page=1

# Sort by creation date
GET /api/users?order_by=created_at&order_direction=desc

# Load relationships
GET /api/users?with[]=posts&with[]=profile

# Relationship counts
GET /api/users?with[]=posts.count&with[]=comments.count
```

### Advanced Filtering
```bash
# Simple filtering
GET /api/users?filters[0][]=email&filters[0][]=user@example.com

# Complex filtering
GET /api/users?filters[0][]=created_at&filters[0][]=>=&filters[0][]=2024-01-01

# JSON format filtering
GET /api/users?filters[0]={"field":"status","operator":"=","value":"active"}

# Filter by related table columns
GET /api/users?filters[0][]=posts.title&filters[0][]=Laravel&filters[0][]=LIKE
```

### Batch Operations
```bash
# Batch create
POST /api/users/batch-store
{
    "users": [
        {"name": "John", "email": "john@example.com"},
        {"name": "Jane", "email": "jane@example.com"}
    ]
}

# Batch update
PUT /api/users/batch-update
{
    "ids": [1, 2, 3],
    "attributes": {"status": "active"}
}

# Batch delete
DELETE /api/users/batch-destroy
{
    "ids": [1, 2, 3]
}
```

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
# Complete CRUD generation
php artisan make:repository User

# Selective generation
php artisan make:repository User --repository --service
php artisan make:repository User --model --migration
php artisan make:repository User --request
```

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

- **PHP**: ^8.1|^8.2|^8.3|^8.4
- **Laravel**: ^10.0|^11.0|^12.0

## 🔄 Version Compatibility

| Laravel Version | PHP Version | Status |
|----------------|-------------|---------|
| 10.x | ^8.1 | ✅ Supported |
| 11.x | ^8.2 | ✅ Supported |
| 12.x | ^8.2 | ✅ Supported |

For detailed version compatibility, see [VERSION_COMPATIBILITY.md](VERSION_COMPATIBILITY.md).

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## 📄 License

This package is open-sourced software licensed under the [MIT License](LICENSE).

## 🆘 Support

- 📧 **Issues**: [GitHub Issues](https://github.com/joesu/laravel-scaffold/issues)
- 📚 **Documentation**: [GitHub Wiki](https://github.com/joesu/laravel-scaffold/wiki)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/joesu/laravel-scaffold/discussions)

## ⭐ Show Your Support

If this package helps you, please give it a ⭐ on GitHub!

---

**Built with ❤️ for the Laravel community** 