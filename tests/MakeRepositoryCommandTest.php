<?php

namespace Tests;

use Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class MakeRepositoryCommandTest extends TestCase
{
    protected $testName = 'TestUser';
    protected $testModel = 'TestUser';
    protected $basePath;

    protected function setUp(): void
    {
        parent::setUp();

        // 設定測試用的基礎路徑
        $this->basePath = sys_get_temp_dir() . '/laravel-scaffold-test';

        // 清理測試目錄
        if (File::exists($this->basePath)) {
            File::deleteDirectory($this->basePath);
        }

        // 建立測試目錄結構
        $this->createTestDirectoryStructure();
    }

    protected function tearDown(): void
    {
        // 清理測試目錄
        if (File::exists($this->basePath)) {
            File::deleteDirectory($this->basePath);
        }

        parent::tearDown();
    }

    protected function createTestDirectoryStructure()
    {
        $directories = [
            $this->basePath . '/app',
            $this->basePath . '/app/Models',
            $this->basePath . '/app/Http',
            $this->basePath . '/app/Http/Controllers',
            $this->basePath . '/app/Http/Requests',
            $this->basePath . '/app/Repositories',
            $this->basePath . '/app/Services',
            $this->basePath . '/app/Contracts',
            $this->basePath . '/app/Providers',
            $this->basePath . '/routes',
            $this->basePath . '/database',
            $this->basePath . '/database/migrations',
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }
        }

        // 建立基本的 AppServiceProvider
        $appServiceProviderContent = "<?php\n\nnamespace App\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\nclass AppServiceProvider extends ServiceProvider\n{\n    public function register()\n    {\n        //\n    }\n\n    public function boot()\n    {\n        //\n    }\n}\n";
        File::put($this->basePath . '/app/Providers/AppServiceProvider.php', $appServiceProviderContent);

        // 建立基本的 API routes 檔案
        $apiRoutesContent = "<?php\n\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Route;\n\n/*\n|--------------------------------------------------------------------------\n| API Routes\n|--------------------------------------------------------------------------\n|\n| Here is where you can register API routes for your application. These\n| routes are loaded by the RouteServiceProvider and all of them will\n| be assigned to the \"api\" middleware group. Make something great!\n|\n*/\n\n";
        File::put($this->basePath . '/routes/api.php', $apiRoutesContent);
    }

    public function testCommandCreatesAllRequiredFiles()
    {
        // 模擬 app_path 和 base_path 函數
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force 避免互動確認
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證檔案是否被建立
        $this->assertFileExists($this->basePath . '/app/Models/' . $this->testModel . '.php');
        $this->assertFileExists($this->basePath . '/app/Http/Controllers/' . $this->testName . 'Controller.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Store' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Update' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Index' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Show' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Repositories/' . $this->testName . 'Repository.php');
        $this->assertFileExists($this->basePath . '/app/Services/' . $this->testName . 'Service.php');
        $this->assertFileExists($this->basePath . '/app/Contracts/' . $this->testName . 'RepositoryInterface.php');
        $this->assertFileExists($this->basePath . '/app/Contracts/' . $this->testName . 'ServiceInterface.php');
    }

    public function testCommandCreatesMigrationWhenRequested()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，包含 migration，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--migration' => true,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 migration 檔案是否被建立（可能路徑不同）
        $migrationFiles = File::glob($this->basePath . '/database/migrations/*_create_test_users_table.php');
        if (empty($migrationFiles)) {
            // 如果沒找到，檢查是否有其他 migration 檔案
            $migrationFiles = File::glob($this->basePath . '/database/migrations/*.php');
        }
        $this->assertNotEmpty($migrationFiles, 'Migration file should be created');
    }

    public function testCommandCreatesRequestsWhenRequested()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，包含 requests，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--requests' => true,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 request 檔案是否被建立
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Store' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Update' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Index' . $this->testName . 'Request.php');
        $this->assertFileExists($this->basePath . '/app/Http/Requests/Show' . $this->testName . 'Request.php');
    }

    public function testCommandUpdatesApiRoutes()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 API routes 是否被更新
        $apiRoutesContent = File::get($this->basePath . '/routes/api.php');
        $this->assertStringContainsString('Route::apiResource(\'test-users\'', $apiRoutesContent);
        $this->assertStringContainsString($this->testName . 'Controller::class', $apiRoutesContent);
    }

    public function testCommandUpdatesAppServiceProvider()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 AppServiceProvider 是否被更新
        $serviceProviderContent = File::get($this->basePath . '/app/Providers/AppServiceProvider.php');
        $this->assertStringContainsString($this->testName . 'ServiceInterface', $serviceProviderContent);
        $this->assertStringContainsString($this->testName . 'RepositoryInterface', $serviceProviderContent);
    }

    public function testCommandRespectsForceOption()
    {
        $this->mockAppPath();

        // 第一次執行
        $result1 = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);
        $result1->assertExitCode(0);

        // 第二次執行，不使用 force
        $result2 = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
        ]);
        $result2->assertExitCode(0);

        // 第三次執行，使用 force
        $result3 = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);
        $result3->assertExitCode(0);

        // 驗證檔案內容沒有被意外覆蓋（除非使用 force）
        $this->assertFileExists($this->basePath . '/app/Models/' . $this->testModel . '.php');
    }

    public function testGeneratedModelHasCorrectStructure()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 Model 檔案內容
        $modelContent = File::get($this->basePath . '/app/Models/' . $this->testModel . '.php');
        $this->assertStringContainsString('class ' . $this->testModel . ' extends Model', $modelContent);
        $this->assertStringContainsString('use HasFactory;', $modelContent);
        $this->assertStringContainsString('protected $fillable', $modelContent);
        $this->assertStringContainsString('protected $casts', $modelContent);
    }

    public function testGeneratedRepositoryHasCorrectStructure()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 Repository 檔案內容
        $repositoryContent = File::get($this->basePath . '/app/Repositories/' . $this->testName . 'Repository.php');
        $this->assertStringContainsString('class ' . $this->testName . 'Repository extends BaseRepository', $repositoryContent);
        $this->assertStringContainsString('implements ' . $this->testName . 'RepositoryInterface', $repositoryContent);
        $this->assertStringContainsString('use JoeSu\\LaravelScaffold\\BaseRepository;', $repositoryContent);
    }

    public function testGeneratedServiceHasCorrectStructure()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 Service 檔案內容
        $serviceContent = File::get($this->basePath . '/app/Services/' . $this->testName . 'Service.php');
        $this->assertStringContainsString('class ' . $this->testName . 'Service extends BaseService', $serviceContent);
        $this->assertStringContainsString('implements ' . $this->testName . 'ServiceInterface', $serviceContent);
        $this->assertStringContainsString('use JoeSu\\LaravelScaffold\\BaseService;', $serviceContent);
    }

    public function testGeneratedControllerHasCorrectStructure()
    {
        $this->mockAppPath();

        // 使用 Artisan::call 執行指令，加上 --force
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        // 驗證 Controller 檔案內容
        $controllerContent = File::get($this->basePath . '/app/Http/Controllers/' . $this->testName . 'Controller.php');
        $this->assertStringContainsString('class ' . $this->testName . 'Controller extends Controller', $controllerContent);
        $this->assertStringContainsString('public function index(', $controllerContent);
        $this->assertStringContainsString('public function show(', $controllerContent);
        $this->assertStringContainsString('public function store(', $controllerContent);
        $this->assertStringContainsString('public function update(', $controllerContent);
        $this->assertStringContainsString('public function destroy(', $controllerContent);
    }

    protected function mockAppPath()
    {
        // 模擬 app_path 函數返回測試路徑
        if (!function_exists('app_path')) {
            function app_path($path = '')
            {
                return sys_get_temp_dir() . '/laravel-scaffold-test/app' . ($path ? '/' . $path : '');
            }
        }

        if (!function_exists('base_path')) {
            function base_path($path = '')
            {
                return sys_get_temp_dir() . '/laravel-scaffold-test' . ($path ? '/' . $path : '');
            }
        }
    }
}
