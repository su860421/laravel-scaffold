<?php

/**
 * Laravel Scaffold 安裝腳本
 * 
 * 這個腳本會幫助你快速設定 Laravel Scaffold 套件
 */

echo "🚀 Laravel Scaffold 安裝腳本\n";
echo "==============================\n\n";

// Check if in Laravel project
if (!file_exists('artisan')) {
    echo "❌ 錯誤：請在 Laravel 專案根目錄中執行此腳本\n";
    exit(1);
}

echo "✅ 檢測到 Laravel 專案\n\n";

// Create necessary directories
$directories = [
    'app/Repositories',
    'app/Services',
    'app/Contracts',
];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
        echo "📁 建立目錄：{$directory}\n";
    } else {
        echo "📁 目錄已存在：{$directory}\n";
    }
}

echo "\n";

// Create example files
echo "📝 建立範例檔案...\n";

// Example Repository Interface
$repositoryInterface = '<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function all();
    public function find($id);
    public function findOrFail($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function where($column, $value);
}';

if (!file_exists('app/Contracts/UserRepositoryInterface.php')) {
    file_put_contents('app/Contracts/UserRepositoryInterface.php', $repositoryInterface);
    echo "✅ 建立：app/Contracts/UserRepositoryInterface.php\n";
}

// Example Repository Class
$repositoryClass = '<?php

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

    // Add custom methods here
    public function findByEmail($email)
    {
        return $this->model->where("email", $email)->first();
    }
}';

if (!file_exists('app/Repositories/UserRepository.php')) {
    file_put_contents('app/Repositories/UserRepository.php', $repositoryClass);
    echo "✅ 建立：app/Repositories/UserRepository.php\n";
}

// Example Service Interface
$serviceInterface = '<?php

namespace App\Contracts;

interface UserServiceInterface
{
    public function all();
    public function find($id);
    public function findOrFail($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function paginate($perPage = 15);
    public function where($column, $value);
}';

if (!file_exists('app/Contracts/UserServiceInterface.php')) {
    file_put_contents('app/Contracts/UserServiceInterface.php', $serviceInterface);
    echo "✅ 建立：app/Contracts/UserServiceInterface.php\n";
}

// Example Service Class
$serviceClass = '<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Contracts\UserRepositoryInterface;
use JoeSu\LaravelScaffold\BaseService;

class UserService extends BaseService implements UserServiceInterface
{
    public function __construct(UserRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    // Add business logic methods here
    public function createUserWithProfile(array $userData, array $profileData)
    {
        $user = $this->repository->create($userData);
        $user->profile()->create($profileData);
        return $user;
    }
}';

if (!file_exists('app/Services/UserService.php')) {
    file_put_contents('app/Services/UserService.php', $serviceClass);
    echo "✅ 建立：app/Services/UserService.php\n";
}

echo "\n";

// Update AppServiceProvider
echo "🔧 更新 AppServiceProvider...\n";

$appServiceProviderPath = 'app/Providers/AppServiceProvider.php';
if (file_exists($appServiceProviderPath)) {
    $content = file_get_contents($appServiceProviderPath);

    // Check if binding already exists
    if (strpos($content, 'UserRepositoryInterface') === false) {
        // Add use statements
        $useStatements = [
            'use App\Contracts\UserRepositoryInterface;',
            'use App\Contracts\UserServiceInterface;',
            'use App\Repositories\UserRepository;',
            'use App\Services\UserService;',
        ];

        foreach ($useStatements as $useStatement) {
            if (strpos($content, $useStatement) === false) {
                $content = str_replace(
                    'use Illuminate\Support\ServiceProvider;',
                    "use Illuminate\Support\ServiceProvider;\n" . $useStatement,
                    $content
                );
            }
        }

        // Add binding
        $bindingCode = '
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);';

        $content = str_replace(
            'public function register()',
            'public function register()' . $bindingCode,
            $content
        );

        file_put_contents($appServiceProviderPath, $content);
        echo "✅ 更新：app/Providers/AppServiceProvider.php\n";
    } else {
        echo "ℹ️  AppServiceProvider 已包含必要的綁定\n";
    }
}

echo "\n";

// Create example Controller
echo "🎮 建立範例 Controller...\n";

$controllerCode = '<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function index()
    {
        $users = $this->userService->all();
        return response()->json($users);
    }

    public function show($id)
    {
        try {
            $user = $this->userService->findOrFail($id);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(["message" => "User not found"], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:8",
        ]);

        $user = $this->userService->create($validated);
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            "name" => "sometimes|string|max:255",
            "email" => "sometimes|email|unique:users,email," . $id,
        ]);

        $user = $this->userService->update($id, $validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        $this->userService->delete($id);
        return response()->json(["message" => "User deleted successfully"]);
    }
}';

if (!file_exists('app/Http/Controllers/UserController.php')) {
    file_put_contents('app/Http/Controllers/UserController.php', $controllerCode);
    echo "✅ 建立：app/Http/Controllers/UserController.php\n";
}

echo "\n";

// Create route example
echo "🛣️  建立路由範例...\n";

$routesCode = '
Route::apiResource("users", UserController::class);
Route::get("users-paginate", [UserController::class, "paginate"])->name("users.paginate");
';

$routesPath = 'routes/api.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);

    if (strpos($routesContent, 'UserController') === false) {
        $routesContent .= $routesCode;
        file_put_contents($routesPath, $routesContent);
        echo "✅ 更新：routes/api.php\n";
    } else {
        echo "ℹ️  路由已包含 UserController\n";
    }
}

echo "\n";

echo "🎉 安裝完成！\n\n";
echo "📋 下一步：\n";
echo "1. 執行：php artisan make:repository YourModel\n";
echo "2. 執行：php artisan make:service YourModel\n";
echo "3. 在 AppServiceProvider 中綁定新的介面\n";
echo "4. 建立對應的 Controller 和路由\n";
echo "5. 開始使用 Laravel Scaffold！\n\n";
echo "📚 更多資訊請參考 README.md\n";
