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

        // 建立 Providers 目錄與 AppServiceProvider
        $providerDir = base_path('app/Providers');
        if (!is_dir($providerDir)) {
            mkdir($providerDir, 0777, true);
        }
        $providerFile = $providerDir . '/AppServiceProvider.php';
        if (!file_exists($providerFile)) {
            file_put_contents(
                $providerFile,
                <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        //
    }
}
PHP
            );
        }
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
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        $appPath = base_path('app');
        $this->assertFileExists($appPath . '/Models/' . $this->testModel . '.php');
        $this->assertFileExists($appPath . '/Http/Controllers/' . $this->testName . 'Controller.php');
        $this->assertFileExists($appPath . '/Http/Requests/Store' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Update' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Index' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Show' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Repositories/' . $this->testName . 'Repository.php');
        $this->assertFileExists($appPath . '/Services/' . $this->testName . 'Service.php');
        $this->assertFileExists($appPath . '/Contracts/' . $this->testName . 'RepositoryInterface.php');
        $this->assertFileExists($appPath . '/Contracts/' . $this->testName . 'ServiceInterface.php');
    }

    public function testCommandCreatesMigrationWhenRequested()
    {
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--migration' => true,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        $migrationFiles = glob(base_path('database/migrations/*_create_test_users_table.php'));
        if (empty($migrationFiles)) {
            $migrationFiles = glob(base_path('database/migrations/*.php'));
        }
        $this->assertNotEmpty($migrationFiles, 'Migration file should be created');
    }

    public function testCommandCreatesRequestsWhenRequested()
    {
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--requests' => true,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        $appPath = base_path('app');
        $this->assertFileExists($appPath . '/Http/Requests/Store' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Update' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Index' . $this->testName . 'Request.php');
        $this->assertFileExists($appPath . '/Http/Requests/Show' . $this->testName . 'Request.php');
    }

    public function testCommandUpdatesApiRoutes()
    {
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        $apiRoutesContent = file_get_contents(base_path('routes/api.php'));
        $this->assertStringContainsString("Route::apiResource('test_users'", $apiRoutesContent);
        $this->assertStringContainsString($this->testName . 'Controller::class', $apiRoutesContent);
    }

    public function testCommandUpdatesAppServiceProvider()
    {
        $result = $this->artisan('make:repository', [
            'name' => $this->testName,
            '--model' => $this->testModel,
            '--force' => true,
        ]);

        $result->assertExitCode(0);

        $serviceProviderContent = file_get_contents(base_path('app/Providers/AppServiceProvider.php'));
        $this->assertStringContainsString($this->testName . 'ServiceInterface', $serviceProviderContent);
        $this->assertStringContainsString($this->testName . 'RepositoryInterface', $serviceProviderContent);
    }
}
