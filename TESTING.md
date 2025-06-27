# Laravel Scaffold Package Testing Guide

This guide will help you test the Laravel Scaffold package in your local environment.

## Prerequisites

- PHP 8.2+
- Laravel 11.0+
- Composer
- A test Laravel project

## Method 1: Local Path Installation (Recommended)

### Step 1: Prepare Your Test Laravel Project

```bash
# Create a new Laravel project for testing
composer create-project laravel/laravel test-scaffold
cd test-scaffold
```

### Step 2: Add Local Repository

Edit your `composer.json` and add the local repository:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../laravel-scaffold"
        }
    ]
}
```

### Step 3: Install the Package

```bash
composer require laravel-scaffold/laravel-scaffold:dev-master
```

### Step 4: Run the Test Script

```bash
# For Linux/Mac
chmod +x ../laravel-scaffold/test-installation.sh
../laravel-scaffold/test-installation.sh

# For Windows
../laravel-scaffold/test-installation.bat
```

## Method 2: Symbolic Link Installation

### Step 1: Create Symbolic Link

```bash
# Linux/Mac
ln -s /path/to/your/laravel-scaffold vendor/laravel-scaffold/laravel-scaffold

# Windows (Run as Administrator)
mklink /D vendor\laravel-scaffold\laravel-scaffold C:\path\to\your\laravel-scaffold
```

### Step 2: Manual Service Provider Registration

Add to `config/app.php`:

```php
'providers' => [
    // ...
    JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider::class,
],
```

## Method 3: Composer Development Installation

### Step 1: Install with Development Version

```bash
composer require laravel-scaffold/laravel-scaffold:dev-master --prefer-source
```

## Manual Testing Steps

### 1. Check Command Availability

```bash
php artisan list | grep scaffold
```

Expected output:
```
make:repository    Create a new repository class and interface with optional model, migration, and requests
```

### 2. Test Basic CRUD Generation

```bash
php artisan make:scaffold User
```

### 3. Check Generated Files

```bash
# Check if all files were created
ls app/Models/User.php
ls app/Repositories/UserRepository.php
ls app/Services/UserService.php
ls app/Http/Controllers/UserController.php
ls app/Contracts/UserServiceInterface.php
```

### 4. Test with Migration

```bash
php artisan make:scaffold Product --migration
```

### 5. Test with Requests

```bash
php artisan make:scaffold Order --requests
```

### 6. Test Complete Generation

```bash
php artisan make:scaffold Category --migration --requests --force
```

### 7. Check API Routes

```bash
php artisan route:list --path=api
```

Expected routes:
```
GET|HEAD   api/users
POST       api/users
GET|HEAD   api/users/{user}
PUT|PATCH  api/users/{user}
DELETE     api/users/{user}
GET|HEAD   api/users/find-by
GET|HEAD   api/users/find-where
GET|HEAD   api/users/exists
GET|HEAD   api/users/count
POST       api/users/update-or-create
GET|HEAD   api/users/all-with-relations
```

### 8. Check Service Provider

```bash
grep -n "UserServiceInterface" app/Providers/AppServiceProvider.php
```

### 9. Test Service Binding

```bash
php artisan tinker
```

In tinker:
```php
app(App\Contracts\UserServiceInterface::class);
// Should return an instance of App\Services\UserService
```

### 10. Test API Endpoints

```bash
# Start the development server
php artisan serve

# In another terminal, test the API
curl http://localhost:8000/api/users
```

## Testing Different Scenarios

### Test 1: Basic Functionality

```bash
php artisan make:scaffold TestBasic
php artisan migrate
```

### Test 2: With Relationships

```bash
php artisan make:scaffold Post --migration
php artisan make:scaffold Comment --migration
```

Add relationships to the generated models and test:

```bash
curl "http://localhost:8000/api/posts?with[]=comments"
```

### Test 3: With Filtering

```bash
curl "http://localhost:8000/api/users?filters[0][]=email&filters[0][]=test@example.com"
```

### Test 4: With Sorting

```bash
curl "http://localhost:8000/api/users?order_by=created_at&order_direction=desc"
```

### Test 5: With Pagination

```bash
curl "http://localhost:8000/api/users?per_page=5"
```

## Troubleshooting

### Issue 1: Command Not Found

**Problem**: `php artisan list` doesn't show `make:repository`

**Solution**:
1. Check if the service provider is registered in `config/app.php`
2. Clear config cache: `php artisan config:clear`
3. Clear route cache: `php artisan route:clear`

### Issue 2: Files Not Generated

**Problem**: Some files are missing after running the command

**Solution**:
1. Check file permissions
2. Use `--force` flag: `php artisan make:scaffold User --force`
3. Check if directories exist: `app/Repositories`, `app/Services`, etc.

### Issue 3: Service Binding Error

**Problem**: `Target [App\Contracts\UserServiceInterface] is not instantiable`

**Solution**:
1. Check if the binding was added to `AppServiceProvider`
2. Clear application cache: `php artisan cache:clear`
3. Restart the application

### Issue 4: API Routes Not Working

**Problem**: API endpoints return 404

**Solution**:
1. Check if routes were added to `routes/api.php`
2. Clear route cache: `php artisan route:clear`
3. Check route list: `php artisan route:list --path=api`

## Performance Testing

### Test Large Dataset

```bash
# Create many records
php artisan tinker
```

```php
// Create 1000 users
for ($i = 1; $i <= 1000; $i++) {
    App\Models\User::create([
        'name' => "User $i",
        'email' => "user$i@example.com",
        'password' => bcrypt('password'),
    ]);
}
```

### Test Pagination Performance

```bash
curl "http://localhost:8000/api/users?per_page=50"
```

### Test Filtering Performance

```bash
curl "http://localhost:8000/api/users?filters[0][]=created_at&filters[0][]=>=&filters[0][]=2024-01-01"
```

## Cleanup

After testing, you can clean up:

```bash
# Remove generated files
rm -rf app/Models/Test*.php
rm -rf app/Repositories/Test*.php
rm -rf app/Services/Test*.php
rm -rf app/Http/Controllers/Test*.php
rm -rf app/Contracts/Test*.php

# Remove migrations
rm -rf database/migrations/*_create_test_*_table.php

# Clean up routes (remove test routes from routes/api.php)
# Clean up AppServiceProvider (remove test bindings)
```

## Success Criteria

The package is working correctly if:

1. ✅ `php artisan make:scaffold User` generates all required files
2. ✅ API routes are accessible and return proper responses
3. ✅ Service bindings work correctly
4. ✅ All CRUD operations function properly
5. ✅ Filtering, sorting, and pagination work
6. ✅ Relationship loading works
7. ✅ Error handling works correctly
8. ✅ Performance is acceptable with large datasets

## Next Steps

After successful testing:

1. **Publish to Packagist**: Prepare for public release
2. **Create Documentation**: Write comprehensive documentation
3. **Add More Features**: Based on testing feedback
4. **Optimize Performance**: If needed
5. **Add More Tests**: Unit and integration tests 