@echo off
echo 🚀 Laravel Scaffold Package Installation Test
echo ==============================================

REM 檢查是否在 Laravel 專案中
if not exist "artisan" (
    echo ❌ Error: This script must be run in a Laravel project root directory
    pause
    exit /b 1
)

echo ✅ Laravel project detected

REM 檢查 composer.json 中是否有套件
findstr "laravel-scaffold/laravel-scaffold" composer.json >nul
if %errorlevel% equ 0 (
    echo ✅ Package found in composer.json
) else (
    echo ❌ Package not found in composer.json
    echo Please add the package to your composer.json:
    echo composer require laravel-scaffold/laravel-scaffold
    pause
    exit /b 1
)

REM 檢查指令是否可用
php artisan list | findstr "make:repository" >nul
if %errorlevel% equ 0 (
    echo ✅ Artisan command 'make:repository' is available
) else (
    echo ❌ Artisan command 'make:repository' not found
    echo Please check if the service provider is registered
    pause
    exit /b 1
)

REM 測試建立 CRUD 架構
echo 🧪 Testing CRUD generation...
php artisan make:scaffold TestUser --force

REM 檢查生成的檔案
echo 📁 Checking generated files...

if exist "app\Models\TestUser.php" (
    echo ✅ app\Models\TestUser.php generated successfully
) else (
    echo ❌ app\Models\TestUser.php not found
)

if exist "app\Repositories\TestUserRepository.php" (
    echo ✅ app\Repositories\TestUserRepository.php generated successfully
) else (
    echo ❌ app\Repositories\TestUserRepository.php not found
)

if exist "app\Services\TestUserService.php" (
    echo ✅ app\Services\TestUserService.php generated successfully
) else (
    echo ❌ app\Services\TestUserService.php not found
)

if exist "app\Http\Controllers\TestUserController.php" (
    echo ✅ app\Http\Controllers\TestUserController.php generated successfully
) else (
    echo ❌ app\Http\Controllers\TestUserController.php not found
)

if exist "app\Contracts\TestUserServiceInterface.php" (
    echo ✅ app\Contracts\TestUserServiceInterface.php generated successfully
) else (
    echo ❌ app\Contracts\TestUserServiceInterface.php not found
)

REM 檢查 API 路由
findstr "TestUser" routes\api.php >nul
if %errorlevel% equ 0 (
    echo ✅ API routes added to routes/api.php
) else (
    echo ❌ API routes not found in routes/api.php
)

REM 檢查服務提供者
findstr "TestUserServiceInterface" app\Providers\AppServiceProvider.php >nul
if %errorlevel% equ 0 (
    echo ✅ Service binding added to AppServiceProvider
) else (
    echo ❌ Service binding not found in AppServiceProvider
)

echo.
echo 🎉 Installation test completed!
echo.
echo Next steps:
echo 1. Run: php artisan migrate (if migration was created)
echo 2. Test API endpoints: GET /api/test-users
echo 3. Check generated files and customize as needed
pause 