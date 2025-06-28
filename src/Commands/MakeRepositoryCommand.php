<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use JoeSu\LaravelScaffold\Stubs\StubManager;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name} 
                            {--migration : Create a migration file}
                            {--requests : Create request classes}
                            {--force : Overwrite existing files}';

    protected $description = 'Create a new repository class and interface with optional migration and requests';

    public function handle()
    {
        $name = $this->argument('name');
        $model = $name; // Model name is always the same as repository name
        $createMigration = $this->option('migration');
        $createRequests = $this->option('requests');
        $force = $this->option('force');

        $this->info("🚀 Creating Laravel Scaffold for: {$name}");
        $this->newLine();

        // Create directories
        $this->createDirectories();

        // Create Model (always needed for Repository and Service)
        $this->createModel($name, $model, $force);

        // Create Migration
        if ($createMigration) {
            $this->createMigration($name, $force);
        }

        // Create Requests
        if ($createRequests) {
            $this->createRequests($name, $force);
        }

        // Generate Repository Interface
        $this->createRepositoryInterface($name, $force);

        // Generate Repository Class
        $this->createRepositoryClass($name, $model, $force);

        // Generate Service Interface
        $this->createServiceInterface($name, $force);

        // Generate Service Class
        $this->createServiceClass($name, $force);

        // Generate Controller
        $this->createController($name, $force);

        // Create API routes
        $this->createApiRoutes($name, $force);

        // Update AppServiceProvider
        $this->updateAppServiceProvider($name, $force);

        $this->newLine();
        $this->info("✅ Laravel Scaffold for {$name} created successfully!");
        $this->info("📝 Service provider bindings have been added automatically.");
        $this->info("🌐 API routes have been added to routes/api.php");

        if ($createMigration) {
            $this->info("🗃️  Run: php artisan migrate");
        }
    }

    protected function createDirectories()
    {
        $directories = [
            app_path('Repositories'),
            app_path('Services'),
            app_path('Contracts'),
            app_path('Http/Requests'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("📁 Created directory: {$directory}");
            }
        }
    }

    protected function createModel($name, $model, $force)
    {
        $modelPath = app_path("Models/{$model}.php");

        if (File::exists($modelPath) && !$force) {
            if (!$this->confirm("Model {$model} already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateModel($name, $model);
        File::put($modelPath, $stub);

        $this->info("✅ Created: app/Models/{$model}.php");
    }

    protected function createMigration($name, $force)
    {
        $tableName = Str::plural(Str::snake($name));
        $migrationName = "create_{$tableName}_table";

        $this->call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName,
        ]);

        $this->info("✅ Created migration for {$tableName} table");
    }

    protected function createRequests($name, $force)
    {
        // 創建對應的 Request 子目錄
        $requestDirectory = app_path("Http/Requests/{$name}");
        if (!File::exists($requestDirectory)) {
            File::makeDirectory($requestDirectory, 0755, true);
            $this->info("📁 Created directory: app/Http/Requests/{$name}");
        }

        $requests = [
            'Store' => "Store{$name}Request",
            'Update' => "Update{$name}Request",
            'Index' => "Index{$name}Request",
            'Show' => "Show{$name}Request",
        ];

        foreach ($requests as $type => $requestName) {
            $requestPath = app_path("Http/Requests/{$name}/{$requestName}.php");

            if (File::exists($requestPath) && !$force) {
                if (!$this->confirm("Request {$requestName} already exists. Overwrite?")) {
                    continue;
                }
            }

            $stub = StubManager::generateRequest($name, $type);
            File::put($requestPath, $stub);

            $this->info("✅ Created: app/Http/Requests/{$name}/{$requestName}.php");
        }
    }

    protected function createRepositoryInterface($name, $force)
    {
        $interfacePath = app_path("Contracts/{$name}RepositoryInterface.php");

        if (File::exists($interfacePath) && !$force) {
            if (!$this->confirm("Interface {$name}RepositoryInterface already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateRepositoryInterface($name);
        File::put($interfacePath, $stub);

        $this->info("✅ Created: app/Contracts/{$name}RepositoryInterface.php");
    }

    protected function createRepositoryClass($name, $model, $force)
    {
        $classPath = app_path("Repositories/{$name}Repository.php");

        if (File::exists($classPath) && !$force) {
            if (!$this->confirm("Repository {$name}Repository already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateRepositoryClass($name, $model);
        File::put($classPath, $stub);

        $this->info("✅ Created: app/Repositories/{$name}Repository.php");
    }

    protected function createServiceInterface($name, $force)
    {
        $interfacePath = app_path("Contracts/{$name}ServiceInterface.php");

        if (File::exists($interfacePath) && !$force) {
            if (!$this->confirm("Interface {$name}ServiceInterface already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateServiceInterface($name);
        File::put($interfacePath, $stub);

        $this->info("✅ Created: app/Contracts/{$name}ServiceInterface.php");
    }

    protected function createServiceClass($name, $force)
    {
        $classPath = app_path("Services/{$name}Service.php");

        if (File::exists($classPath) && !$force) {
            if (!$this->confirm("Service {$name}Service already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateServiceClass($name);
        File::put($classPath, $stub);

        $this->info("✅ Created: app/Services/{$name}Service.php");
    }

    protected function createController($name, $force)
    {
        $controllerPath = app_path("Http/Controllers/{$name}Controller.php");

        if (File::exists($controllerPath) && !$force) {
            if (!$this->confirm("Controller {$name}Controller already exists. Overwrite?")) {
                return;
            }
        }

        $stub = StubManager::generateController($name);
        File::put($controllerPath, $stub);

        $this->info("✅ Created: app/Http/Controllers/{$name}Controller.php");
    }

    protected function getTableName($name)
    {
        return Str::plural(Str::snake($name));
    }

    protected function createApiRoutes($name, $force)
    {
        $routesPath = base_path('routes/api.php');
        $routeName = Str::plural(Str::snake($name));

        // Read existing API routes file
        $existingContent = '';
        if (File::exists($routesPath)) {
            $existingContent = File::get($routesPath);
        }

        // Check if route already exists
        if (strpos($existingContent, "Route::apiResource('{$routeName}'") !== false) {
            if (!$force && !$this->confirm("API routes for {$routeName} already exist. Add anyway?")) {
                return;
            }
        }

        // Prepare new route content - only basic CRUD
        $newRoute = "\nRoute::apiResource('{$routeName}', {$name}Controller::class);\n";

        // If file doesn't exist, create basic structure
        if (!File::exists($routesPath)) {
            $basicContent = "<?php\n\nuse Illuminate\\Http\\Request;\nuse Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{$name}Controller;\n\n/*\n|--------------------------------------------------------------------------\n| API Routes\n|--------------------------------------------------------------------------\n|\n| Here is where you can register API routes for your application. These\n| routes are loaded by the RouteServiceProvider and all of them will\n| be assigned to the \"api\" middleware group. Make something great!\n|\n*/\n\n";
            File::put($routesPath, $basicContent . $newRoute);
        } else {
            // Check if use statement needs to be added
            if (strpos($existingContent, "use App\\Http\\Controllers\\{$name}Controller") === false) {
                // Find the position of the last use statement
                $lines = explode("\n", $existingContent);
                $lastUseIndex = -1;

                for ($i = 0; $i < count($lines); $i++) {
                    if (strpos(trim($lines[$i]), 'use ') === 0) {
                        $lastUseIndex = $i;
                    }
                }

                if ($lastUseIndex >= 0) {
                    // Add new use statement after the last use statement
                    array_splice($lines, $lastUseIndex + 1, 0, "use App\\Http\\Controllers\\{$name}Controller;");
                    $existingContent = implode("\n", $lines);
                } else {
                    // If no use statement found, add after namespace
                    $existingContent = str_replace(
                        'use Illuminate\Support\Facades\Route;',
                        "use Illuminate\\Support\\Facades\\Route;\nuse App\\Http\\Controllers\\{$name}Controller;",
                        $existingContent
                    );
                }

                File::put($routesPath, $existingContent);
            }

            // Add new route at the end of file
            File::append($routesPath, $newRoute);
        }

        $this->info("✅ Added API routes for {$routeName} to routes/api.php");
    }

    protected function updateAppServiceProvider($name, $force)
    {
        $serviceProviderPath = app_path('Providers/AppServiceProvider.php');

        if (!File::exists($serviceProviderPath)) {
            $this->warn("⚠️  AppServiceProvider not found. Please create it manually.");
            return;
        }

        $content = File::get($serviceProviderPath);

        // Check if binding already exists
        if (strpos($content, "{$name}ServiceInterface") !== false) {
            if (!$force && !$this->confirm("Service provider bindings for {$name} already exist. Add anyway?")) {
                return;
            }
        }

        // Prepare new binding content
        $newBinding = "\n        \$this->app->bind({$name}ServiceInterface::class, {$name}Service::class);\n";
        $newBinding .= "        \$this->app->bind({$name}RepositoryInterface::class, {$name}Repository::class);\n";

        // Check if register method exists
        if (strpos($content, 'public function register()') !== false) {
            // Find the register method and insert binding before the closing brace
            $lines = explode("\n", $content);
            $registerStartIndex = -1;
            $registerEndIndex = -1;
            $braceCount = 0;
            $inRegisterMethod = false;

            for ($i = 0; $i < count($lines); $i++) {
                $line = trim($lines[$i]);

                if (strpos($line, 'public function register()') !== false) {
                    $inRegisterMethod = true;
                    $registerStartIndex = $i;
                }

                if ($inRegisterMethod) {
                    $braceCount += substr_count($line, '{');
                    $braceCount -= substr_count($line, '}');

                    if ($braceCount === 0 && $registerStartIndex !== $i) {
                        $registerEndIndex = $i;
                        break;
                    }
                }
            }

            if ($registerEndIndex !== -1) {
                // Insert binding before the closing brace
                array_splice($lines, $registerEndIndex, 0, $newBinding);
                $content = implode("\n", $lines);
            }
        } else {
            // If no register method, add one
            $content = str_replace(
                'class AppServiceProvider extends ServiceProvider',
                "class AppServiceProvider extends ServiceProvider\n{\n    public function register()\n    {" . $newBinding . "    }\n\n    public function boot()\n    {\n        //\n    }",
                $content
            );
        }

        // Add necessary use statements
        $useStatements = "\nuse App\\Contracts\\{$name}ServiceInterface;\nuse App\\Services\\{$name}Service;\nuse App\\Contracts\\{$name}RepositoryInterface;\nuse App\\Repositories\\{$name}Repository;";

        if (strpos($content, "use App\\Contracts\\{$name}ServiceInterface") === false) {
            $content = str_replace(
                'namespace App\\Providers;',
                'namespace App\\Providers;' . $useStatements,
                $content
            );
        }

        File::put($serviceProviderPath, $content);
        $this->info("✅ Updated AppServiceProvider with {$name} bindings");
    }
}
