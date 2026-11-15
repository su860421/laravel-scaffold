# 版本相容性說明

## 支援的版本(2.x)

| Laravel | PHP | 狀態 |
|---------|-----|------|
| 13.x | 8.3 – 8.5 | ✅ 支援 |
| 12.x | 8.3 – 8.5 | ✅ 支援 |

每個組合都由 GitHub Actions 測試(見 `.github/workflows/tests.yml`)。

## 舊版本

Laravel 10、11 與 PHP 8.1、8.2 已結束官方維護,2.x 不再支援。
仍在使用這些版本的專案請安裝 1.x:

```bash
composer require joesu/laravel-scaffold:^1.1
```

| 套件版本 | Laravel | PHP |
|----------|---------|-----|
| 2.x | 12.x、13.x | 8.3 – 8.5 |
| 1.x | 10.x、11.x、12.x | 8.1 – 8.4 |

## 安裝

```bash
composer require joesu/laravel-scaffold
```

## Laravel 11 之後的 API 路由

Laravel 11 起的專案骨架預設不載入 `routes/api.php`。
執行 `php artisan make:repository` 時,套件會自動在 `bootstrap/app.php` 的
`withRouting()` 補上:

```php
api: __DIR__.'/../routes/api.php',
```

若 `withRouting()` 是自訂寫法而無法自動加入,指令會顯示警告,請手動補上這一行。

## 從 1.x 升級到 2.x

1. 確認專案使用 PHP 8.3 以上、Laravel 12 以上
2. 執行 `composer require joesu/laravel-scaffold:^2.0`
3. 既有產生的 Controller 若使用 `$request->get('per_page', 0)`,
   請改為 `$request->integer('per_page')`,否則帶 `per_page` 參數時會發生型別錯誤

## 回報問題

請在 GitHub Issues 回報,並附上:

1. Laravel 版本:`php artisan --version`
2. PHP 版本:`php --version`
