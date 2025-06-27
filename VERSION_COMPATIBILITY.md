# 版本相容性說明

## 支援的版本

### Laravel 版本
- **Laravel 10.x** ✅
- **Laravel 11.x** ✅  
- **Laravel 12.x** ✅

### PHP 版本
- **PHP 8.1+** ✅
- **PHP 8.2+** ✅
- **PHP 8.3+** ✅
- **PHP 8.4+** ✅

## 版本差異說明

### Laravel 10 vs 11 vs 12 的差異

#### Laravel 10
- 最低 PHP 版本：8.1
- 使用舊版的 Service Provider 註冊方式
- 支援舊版的 Artisan 命令語法

#### Laravel 11
- 最低 PHP 版本：8.2
- 改進了 Service Provider 註冊
- 新的 Artisan 命令語法
- 更好的錯誤處理

#### Laravel 12
- 最低 PHP 版本：8.2
- 進一步改進的架構
- 更好的效能優化

## 安裝指南

### 在 Laravel 10 專案中安裝
```bash
composer require joesu/laravel-scaffold
```

### 在 Laravel 11 專案中安裝
```bash
composer require joesu/laravel-scaffold
```

### 在 Laravel 12 專案中安裝
```bash
composer require joesu/laravel-scaffold
```

## 相容性測試

套件已經過以下版本的測試：

- Laravel 10.0 - 10.x
- Laravel 11.0 - 11.x  
- Laravel 12.0 - 12.x

## 已知問題

目前沒有已知的版本相容性問題。

## 回報問題

如果您在使用特定 Laravel 版本時遇到問題，請：

1. 檢查您的 Laravel 版本：`php artisan --version`
2. 檢查您的 PHP 版本：`php --version`
3. 在 GitHub Issues 中回報問題，並提供版本資訊

## 升級指南

### 從 Laravel 10 升級到 11
1. 更新 `composer.json` 中的 Laravel 版本
2. 執行 `composer update`
3. 檢查 Laravel 11 的升級指南
4. 測試套件功能

### 從 Laravel 11 升級到 12
1. 更新 `composer.json` 中的 Laravel 版本
2. 執行 `composer update`
3. 檢查 Laravel 12 的升級指南
4. 測試套件功能 