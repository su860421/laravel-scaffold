# Laravel Scaffold 使用範例

## 快速開始

### 1. 基本建立

```bash
# 建立基本的 Repository 和 Service
php artisan make:repository User
```

這會建立：
- Model: `app/Models/User.php`
- Repository Interface: `app/Contracts/UserRepositoryInterface.php`
- Repository: `app/Repositories/UserRepository.php`
- Service Interface: `app/Contracts/UserServiceInterface.php`
- Service: `app/Services/UserService.php`
- Controller: `app/Http/Controllers/UserController.php`

### 2. 完整建立（包含 Migration 和 Request）

```bash
# 建立完整的 CRUD 架構
php artisan make:repository User --migration --requests
```

這會額外建立：
- Migration: `database/migrations/xxxx_xx_xx_create_users_table.php`
- Store Request: `app/Http/Requests/StoreUserRequest.php`
- Update Request: `app/Http/Requests/UpdateUserRequest.php`

### 3. 自訂 Model 名稱

```bash
# 使用自訂的 Model 名稱
php artisan make:repository User --model=AppUser --migration --requests
```

## 使用範例

### API 路由設定

在 `routes/api.php` 中添加：

```php
use App\Http\Controllers\UserController;

// 基本 CRUD 路由
Route::apiResource('users', UserController::class);

// 批量操作路由
Route::post('users/batch-store', [UserController::class, 'batchStore']);
Route::put('users/batch-update', [UserController::class, 'batchUpdate']);
Route::delete('users/batch-destroy', [UserController::class, 'batchDestroy']);

// 軟刪除操作路由
Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete']);
Route::post('users/{id}/restore', [UserController::class, 'restore']);

// 實用操作路由
Route::get('users/exists', [UserController::class, 'exists']);
Route::get('users/count', [UserController::class, 'count']);
Route::post('users/update-or-create', [UserController::class, 'updateOrCreate']);
Route::get('users/first-or-new', [UserController::class, 'firstOrNew']);
```

### API 使用範例

#### 1. 取得所有使用者

```bash
GET /api/users
```

#### 2. 分頁查詢

```bash
GET /api/users?per_page=10
```

#### 3. 排序

```bash
GET /api/users?order_by=name&order_direction=asc
```

#### 4. 載入關係

```bash
GET /api/users?with[]=posts&with[]=profile
```

#### 5. 關係計數

```bash
GET /api/users?with[]=posts.count&with[]=comments.count
```

#### 6. 過濾條件

```bash
GET /api/users?filters[0][0]=status&filters[0][1]=active
```

#### 7. 關聯表欄位過濾

```bash
GET /api/users?filters[0][0]=posts.title&filters[0][1]=LIKE&filters[0][2]=%Laravel%
```

#### 8. 選擇欄位

```bash
GET /api/users?columns[]=id&columns[]=name&columns[]=email
```

#### 9. 查找特定使用者（支援關聯載入）

```bash
GET /api/users/1?with[]=posts&with[]=profile
```

#### 10. 建立使用者

```bash
POST /api/users
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "status": "active"
}
```

#### 11. 批量建立使用者

```bash
POST /api/users/batch-store
Content-Type: application/json

{
    "users": [
        {"name": "John", "email": "john@example.com", "password": "password"},
        {"name": "Jane", "email": "jane@example.com", "password": "password"}
    ]
}
```

#### 12. 更新使用者

```bash
PUT /api/users/1
Content-Type: application/json

{
    "name": "John Updated",
    "status": "inactive"
}
```

#### 13. 批量更新使用者

```bash
PUT /api/users/batch-update
Content-Type: application/json

{
    "ids": [1, 2, 3],
    "attributes": {"status": "active"}
}
```

#### 14. 刪除使用者

```bash
DELETE /api/users/1
```

#### 15. 批量刪除使用者

```bash
DELETE /api/users/batch-destroy
Content-Type: application/json

{
    "ids": [1, 2, 3]
}
```

#### 16. 強制刪除使用者

```bash
DELETE /api/users/1/force-delete
```

#### 17. 恢復已刪除的使用者

```bash
POST /api/users/1/restore
```

## Repository 使用範例

### 基本操作

```php
<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use JoeSu\LaravelScaffold\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    // 覆寫允許排序的欄位（防止 SQL injection）
    protected function getAllowedSortColumns(): array
    {
        return ['id', 'name', 'email', 'created_at', 'updated_at'];
    }

    // 查找使用者並載入關聯
    public function findUserWithPosts($id)
    {
        return $this->find($id, ['*'], ['posts', 'profile']);
    }

    // 取得活躍使用者
    public function getActiveUsers()
    {
        return $this->index(0, null, null, [], ['*'], [['status', 'active']]);
    }

    // 取得有特定文章標題的使用者
    public function getUsersWithPostTitle($title)
    {
        return $this->index(0, null, null, [], ['*'], [['posts.title', 'LIKE', "%{$title}%"]]);
    }

    // 批量建立使用者
    public function createManyUsers(array $users)
    {
        return $this->batchCreate($users);
    }
}
```

### Service 使用範例

```php
<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // 業務邏輯：建立使用者並發送歡迎郵件
    public function createUserWithWelcomeEmail(array $userData)
    {
        $user = $this->repository->create($userData);
        
        // 發送歡迎郵件
        Mail::to($user->email)->send(new WelcomeEmail($user));
        
        return $user;
    }

    // 業務邏輯：批量建立使用者
    public function createUsersInBatch(array $usersData)
    {
        $result = $this->repository->batchCreate($usersData);
        
        // 記錄批量建立
        Log::info('Batch created users', ['count' => count($usersData)]);
        
        return $result;
    }

    // 業務邏輯：取得使用者及其文章
    public function getUserWithPosts($id)
    {
        return $this->repository->find($id, ['*'], ['posts', 'posts.comments']);
    }

    // 業務邏輯：軟刪除使用者
    public function softDeleteUser($id)
    {
        $user = $this->repository->find($id);
        
        // 發送刪除通知
        Mail::to($user->email)->send(new AccountDeletedEmail($user));
        
        return $this->repository->delete($id);
    }
}
```

## 進階使用範例

### 複雜查詢

```php
// 分頁查詢活躍使用者，按建立時間排序，載入文章關聯
$users = $userRepository->index(
    perPage: 10,
    orderBy: 'created_at',
    orderDirection: 'desc',
    relationships: ['posts.count', 'comments'],
    columns: ['id', 'name', 'email', 'status'],
    filters: [
        ['status', 'active'],
        ['posts.title', 'LIKE', '%Laravel%'],
        ['created_at', '>=', '2024-01-01']
    ]
);

// 查找特定使用者並載入關聯
$user = $userRepository->find(1, ['id', 'name', 'email'], ['posts', 'profile']);

// 批量操作
$userRepository->batchCreate($users);
$userRepository->batchUpdate([1, 2, 3], ['status' => 'active']);
$userRepository->batchDelete([1, 2, 3]);

// 軟刪除操作
$userRepository->forceDelete(1);
$userRepository->restore(1);
```

### 關聯表欄位過濾

```php
// 過濾有特定文章標題的使用者
$users = $userRepository->index(
    0, null, null, [], ['*'], 
    [['posts.title', 'LIKE', '%Laravel%']]
);

// 過濾有特定分類的商品
$products = $productRepository->index(
    0, null, null, [], ['*'], 
    [['category.name', '=', 'Electronics']]
);

// 過濾有特定標籤的文章
$posts = $postRepository->index(
    0, null, null, [], ['*'], 
    [['tags.name', 'IN', ['Laravel', 'PHP']]]
);
```

### 大量資料處理

```php
// 分塊處理大量資料
$userRepository->chunk(1000, function ($users) {
    foreach ($users as $user) {
        // 處理每個使用者
        $user->update(['processed' => true]);
    }
});

// 使用 cursor 處理大量資料
$userRepository->cursor()->each(function ($user) {
    // 處理每個使用者
    $user->update(['processed' => true]);
});
```

## 錯誤處理

```php
use JoeSu\LaravelScaffold\Exceptions\RepositoryException;

try {
    $user = $userRepository->find(999);
} catch (RepositoryException $e) {
    // 處理 Repository 錯誤
    Log::error('Repository error: ' . $e->getMessage());
    return response()->json(['error' => $e->getMessage()], $e->getCode());
} catch (\Exception $e) {
    // 處理其他錯誤
    Log::error('General error: ' . $e->getMessage());
    return response()->json(['error' => 'Internal server error'], 500);
}
```

## 最佳實踐

### 1. 排序欄位白名單

```php
protected function getAllowedSortColumns(): array
{
    return ['id', 'name', 'email', 'created_at', 'updated_at'];
}
```

### 2. 關聯載入優化

```php
// 好的做法：只載入需要的關聯
$users = $userRepository->index(0, null, null, ['posts.count'], ['id', 'name']);

// 避免：載入不必要的關聯
$users = $userRepository->index(0, null, null, ['posts', 'comments', 'profile', 'settings']);
```

### 3. 批量操作

```php
// 好的做法：使用批量操作
$userRepository->batchUpdate($userIds, ['status' => 'active']);

// 避免：循環更新
foreach ($userIds as $id) {
    $userRepository->update($id, ['status' => 'active']);
}
```

### 4. 軟刪除使用

```php
// 一般刪除（軟刪除）
$userRepository->delete($id);

// 強制刪除（永久刪除）
$userRepository->forceDelete($id);

// 恢復已刪除的記錄
$userRepository->restore($id);
``` 