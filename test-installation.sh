#!/bin/bash

echo "🚀 Laravel Scaffold Package Installation Test"
echo "=============================================="

# 檢查是否在 Laravel 專案中
if [ ! -f "artisan" ]; then
    echo "❌ Error: This script must be run in a Laravel project root directory"
    exit 1
fi

echo "✅ Laravel project detected"

# 檢查 composer.json 中是否有套件
if grep -q "laravel-scaffold/laravel-scaffold" composer.json; then
    echo "✅ Package found in composer.json"
else
    echo "❌ Package not found in composer.json"
    echo "Please add the package to your composer.json:"
    echo "composer require laravel-scaffold/laravel-scaffold"
    exit 1
fi

# 檢查指令是否可用
if php artisan list | grep -q "make:repository"; then
    echo "✅ Artisan command 'make:repository' is available"
else
    echo "❌ Artisan command 'make:repository' not found"
    echo "Please check if the service provider is registered"
    exit 1
fi

# 測試建立 CRUD 架構
echo "🧪 Testing CRUD generation..."
php artisan make:scaffold TestUser --force

# 檢查生成的檔案
echo "📁 Checking generated files..."

files=(
    "app/Models/TestUser.php"
    "app/Repositories/TestUserRepository.php"
    "app/Services/TestUserService.php"
    "app/Http/Controllers/TestUserController.php"
    "app/Contracts/TestUserServiceInterface.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file generated successfully"
    else
        echo "❌ $file not found"
    fi
done

# 檢查 API 路由
if grep -q "TestUser" routes/api.php; then
    echo "✅ API routes added to routes/api.php"
else
    echo "❌ API routes not found in routes/api.php"
fi

# 檢查服務提供者
if grep -q "TestUserServiceInterface" app/Providers/AppServiceProvider.php; then
    echo "✅ Service binding added to AppServiceProvider"
else
    echo "❌ Service binding not found in AppServiceProvider"
fi

echo ""
echo "🎉 Installation test completed!"
echo ""
echo "Next steps:"
echo "1. Run: php artisan migrate (if migration was created)"
echo "2. Test API endpoints: GET /api/test-users"
echo "3. Check generated files and customize as needed" 