# Laravel Scaffold Package

A powerful Laravel package that provides a complete implementation of Repository and Service patterns for rapid API development.

## Requirements

- **PHP**: ^8.1
- **Laravel**: ^10.0|^11.0|^12.0

## Version Compatibility

This package supports multiple Laravel versions:

| Laravel Version | PHP Version | Status |
|----------------|-------------|---------|
| 10.x | ^8.1 | ✅ Supported |
| 11.x | ^8.2 | ✅ Supported |
| 12.x | ^8.2 | ✅ Supported |

For detailed version compatibility information, see [VERSION_COMPATIBILITY.md](VERSION_COMPATIBILITY.md).

## Architecture

The package provides a complete layered architecture with base interfaces:

### Base Interfaces
- **BaseRepositoryInterface**: Defines all basic repository methods
- **BaseServiceInterface**: Defines all basic service methods

### Base Classes
- **BaseRepository**: Implements BaseRepositoryInterface with complete CRUD functionality
- **BaseService**: Implements BaseServiceInterface with service layer logic

### Generated Files
When you run `php artisan make:repository User`, the following files are generated:

```
app/
├── Models/
│   └── User.php                    # Eloquent Model
├── Http/
│   ├── Controllers/
│   │   └── UserController.php      # API Controller
│   └── Requests/
│       ├── StoreUserRequest.php    # Store validation
│       └── UpdateUserRequest.php   # Update validation
├── Repositories/
│   └── UserRepository.php          # Extends BaseRepository
├── Services/
│   └── UserService.php             # Extends BaseService
└── Contracts/
    ├── UserRepositoryInterface.php # Extends BaseRepositoryInterface
    └── UserServiceInterface.php    # Extends BaseServiceInterface
```

## Features

### BaseRepository Features
- **Complete CRUD operations**: create, read, update, delete
- **Advanced query methods**: index, find with relationships
- **Relationship loading**: support for with and withCount
- **Filtering system**: support for JSON and array format filters
- **Relation field filtering**: filter by related table columns (e.g., 'user.name')
- **Sorting mechanism**: configurable sort column whitelist
- **Bulk data processing**: chunk() and cursor() methods
- **Batch operations**: batchCreate, batchUpdate, batchDelete
- **Soft delete support**: forceDelete, restore
- **Error handling**: custom RepositoryException with multilingual support

### BaseService Features
- **Basic CRUD operations**: create, read, update, delete
- **Advanced queries**: index, find with relationships
- **Batch operations**: batchCreate, batchUpdate, batchDelete
- **Soft delete operations**: forceDelete, restore
- **Utility methods**: updateOrCreate

### Auto-generation Features
- **One-click complete CRUD architecture**
- **Auto-generate Model, Migration, Request**
- **Auto-generate Controller, Repository, Service**
- **Auto-generate interfaces and bindings**
- **Auto-create API routes**
- **Auto-register AppServiceProvider**

## Installation

```bash
composer require laravel-scaffold/laravel-scaffold
```

## Quick Start

### 1. Create Complete CRUD Architecture

```bash
php artisan make:scaffold User
```

This will automatically generate:
- `app/Models/User.php`
- `database/migrations/xxxx_xx_xx_create_users_table.php`
- `app/Http/Requests/UserRequest.php`
- `app/Http/Controllers/UserController.php`
- `app/Repositories/UserRepository.php`
- `app/Services/UserService.php`
- `app/Contracts/UserServiceInterface.php`
- **Auto-add API routes to `routes/api.php`**
- **Auto-register service bindings to `AppServiceProvider`**

### 2. Selective File Generation

```bash
# Only create Repository and Service
php artisan make:scaffold User --repository --service

# Create Model and Migration
php artisan make:scaffold User --model --migration

# Create Request classes
php artisan make:scaffold User --request
```

## API Routes

The package automatically adds the following routes to `routes/api.php`:

```php
// User API Routes
Route::apiResource('users', UserController::class);

// Batch operations
Route::post('users/batch-store', [UserController::class, 'batchStore']);
Route::put('users/batch-update', [UserController::class, 'batchUpdate']);
Route::delete('users/batch-destroy', [UserController::class, 'batchDestroy']);

// Soft delete operations
Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);
Route::post('users/{id}/restore', [UserController::class, 'restore']);

// Utility operations
Route::get('users/exists', [UserController::class, 'exists']);
Route::get('users/count', [UserController::class, 'count']);
Route::post('users/update-or-create', [UserController::class, 'updateOrCreate']);
```

## AppServiceProvider Registration

The package automatically registers service bindings in `AppServiceProvider`:

```php
public function register(): void
{
    // User Service Binding
    $this->app->bind(UserServiceInterface::class, UserService::class);
}
```

## API Usage Examples

### Basic Queries
```bash
# Get all users
GET /api/users

# Pagination
GET /api/users?per_page=10

# Sorting
GET /api/users?order_by=created_at&order_direction=desc

# Load relationships
GET /api/users?with[]=posts&with[]=comments

# Relationship counts
GET /api/users?with[]=posts.count&with[]=comments.count

# Find specific user with relationships
GET /api/users/1?with[]=posts&with[]=profile
```

### Filtering
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
# Batch create users
POST /api/users/batch-store
{
    "users": [
        {"name": "John", "email": "john@example.com", "password": "password"},
        {"name": "Jane", "email": "jane@example.com", "password": "password"}
    ]
}

# Batch update users
PUT /api/users/batch-update
{
    "ids": [1, 2, 3],
    "attributes": {"status": "active"}
}

# Batch delete users
DELETE /api/users/batch-destroy
{
    "ids": [1, 2, 3]
}
```

### Soft Delete Operations
```bash
# Force delete user
DELETE /api/users/1/force-delete

# Restore deleted user
POST /api/users/1/restore
```

### Advanced Queries
```bash
# Check existence
GET /api/users/exists?conditions[0][]=email&conditions[0][]=user@example.com

# Count records
GET /api/users/count?conditions[0][]=status&conditions[0][]=active

# Update or create
POST /api/users/update-or-create
```

## Repository Usage Examples

### Basic Operations
```php
// Find user with relationships
$user = $userRepository->find(1, ['*'], ['posts', 'profile']);

// Index with filters and relationships
$users = $userRepository->index(
    perPage: 10,
    orderBy: 'created_at',
    orderDirection: 'desc',
    relationships: ['posts.count', 'comments'],
    columns: ['id', 'name', 'email'],
    filters: [
        ['status', 'active'],
        ['posts.title', 'LIKE', '%Laravel%']
    ]
);

// Batch operations
$userRepository->batchCreate($users);
$userRepository->batchUpdate([1, 2, 3], ['status' => 'active']);
$userRepository->batchDelete([1, 2, 3]);

// Soft delete operations
$userRepository->forceDelete(1);
$userRepository->restore(1);
```

## Error Handling

The package uses custom `RepositoryException` for error handling with multilingual support:

```php
use JoeSu\LaravelScaffold\Exceptions\RepositoryException;

try {
    $user = $this->userService->find($id);
} catch (RepositoryException $e) {
    // Error messages are automatically translated based on current locale
    return ApiResponse::error($e->getMessage(), $e->getCode());
}
```

### Error Message Examples

```php
// English messages
"Record with ID 123 not found"
"Failed to create model: SQLSTATE[23000]: Integrity constraint violation"

// Chinese Traditional messages (when locale is zh-TW)
"找不到 ID 為 123 的記錄"
"建立模型失敗: SQLSTATE[23000]: Integrity constraint violation"
```

## Multilingual Support

The package includes built-in multilingual support for error messages:

### Supported Languages
- English (en)
- Chinese Traditional (zh-TW)

### Publishing Language Files

```bash
# Publish all language files
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider" --tag=lang

# Or publish specific components
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider" --tag=config
```

### Customizing Messages

After publishing, you can customize messages in:
- `resources/lang/vendor/laravel-scaffold/en/messages.php`
- `resources/lang/vendor/laravel-scaffold/zh-TW/messages.php`

### Adding New Languages

1. Create a new language directory: `resources/lang/vendor/laravel-scaffold/ja/`
2. Copy `messages.php` from English and translate the messages
3. Set your application locale: `App::setLocale('ja')`

## Configuration

The package automatically registers necessary service providers and bindings. For custom configuration, publish the config file:

```bash
php artisan vendor:publish --provider="JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider"
```

## Testing

```bash
composer test
```

## License

MIT License

## Contributing

Welcome to submit Issues and Pull Requests!

## Examples

For detailed usage examples, see the `examples/` directory:
- `examples/usage_example.md` - Complete usage guide
- `examples/UserController.php` - Controller example
- `examples/migration_example.php` - Migration example
- `examples/api_routes_example.php` - API routes example
- `examples/app_service_provider_example.php` - Service provider example 