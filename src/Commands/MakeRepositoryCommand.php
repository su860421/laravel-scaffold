<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MakeRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name} 
                            {--model= : The model name}
                            {--migration : Create a migration file}
                            {--requests : Create request classes}
                            {--force : Overwrite existing files}';

    protected $description = 'Create a new repository class and interface with optional model, migration, and requests';

    public function handle()
    {
        $name = $this->argument('name');
        $model = $this->option('model') ?: $name;
        $createMigration = $this->option('migration');
        $createRequests = $this->option('requests');
        $force = $this->option('force');

        $this->info("🚀 Creating Laravel Scaffold for: {$name}");
        $this->newLine();

        // Create directories
        $this->createDirectories();

        // Create Model
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

        $stub = $this->getModelStub($name, $model);
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
        $requests = [
            'Store' => "Store{$name}Request",
            'Update' => "Update{$name}Request",
            'Index' => "Index{$name}Request",
            'Show' => "Show{$name}Request",
        ];

        foreach ($requests as $type => $requestName) {
            $requestPath = app_path("Http/Requests/{$requestName}.php");

            if (File::exists($requestPath) && !$force) {
                if (!$this->confirm("Request {$requestName} already exists. Overwrite?")) {
                    continue;
                }
            }

            $stub = $this->getRequestStub($name, $type);
            File::put($requestPath, $stub);

            $this->info("✅ Created: app/Http/Requests/{$requestName}.php");
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

        $stub = $this->getRepositoryInterfaceStub($name);
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

        $stub = $this->getRepositoryClassStub($name, $model);
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

        $stub = $this->getServiceInterfaceStub($name);
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

        $stub = $this->getServiceClassStub($name);
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

        $stub = $this->getControllerStub($name);
        File::put($controllerPath, $stub);

        $this->info("✅ Created: app/Http/Controllers/{$name}Controller.php");
    }

    protected function getModelStub($name, $model)
    {
        $tableName = Str::plural(Str::snake($name));

        return "<?php

declare(strict_types=1);

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\HasMany;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;

class {$model} extends Model
{
    use HasFactory;

    protected \$fillable = [
        // Add fillable fields here
        // Examples:
        // 'name',
        // 'email',
        // 'status',
    ];

    protected \$casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // Add other type casts here
        // Examples:
        // 'is_active' => 'boolean',
        // 'settings' => 'array',
    ];

    // Define relationship methods here
    // Examples:
    // public function posts(): HasMany
    // {
    //     return \$this->hasMany(Post::class);
    // }
}
";
    }

    protected function getRequestStub($name, $type)
    {
        $requestName = "{$type}{$name}Request";
        $rules = '';
        if ($type === 'Index') {
            $rules = "        return [\n            'per_page' => ['integer', 'min:1', 'max:100'],\n            'order_by' => ['string'],\n            'order_direction' => ['in:asc,desc'],\n            'with' => ['array'],\n            'columns' => ['array'],\n            'filters' => ['array'],\n        ];";
        } elseif ($type === 'Show') {
            $rules = "        return [\n            'id' => ['required', 'integer', 'min:1'],\n            'columns' => ['array'],\n            'with' => ['array'],\n        ];";
        } else {
            $rules = "        return [];";
        }

        return "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Requests;\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\n\nclass {$requestName} extends FormRequest\n{\n    /**\n     * Determine if the user is authorized to make this request.\n     */\n    public function authorize(): bool\n    {\n        return true;\n    }\n\n    /**\n     * Get the validation rules that apply to the request.\n     */\n    public function rules(): array\n    {\n{$rules}\n    }\n}\n";
    }

    protected function getTableName($name)
    {
        return Str::plural(Str::snake($name));
    }

    protected function getRepositoryInterfaceStub($name)
    {
        return "<?php

declare(strict_types=1);

namespace App\\Contracts;

use JoeSu\\LaravelScaffold\\BaseRepositoryInterface;

interface {$name}RepositoryInterface extends BaseRepositoryInterface
{
    // Add custom methods here
}
";
    }

    protected function getRepositoryClassStub($name, $model)
    {
        return "<?php

declare(strict_types=1);

namespace App\\Repositories;

use App\\Contracts\\{$name}RepositoryInterface;
use App\\Models\\{$model};
use JoeSu\\LaravelScaffold\\BaseRepository;

class {$name}Repository extends BaseRepository implements {$name}RepositoryInterface
{
    public function __construct({$model} \$model)
    {
        parent::__construct(\$model);
    }

    // Add custom methods here
}
";
    }

    protected function getServiceInterfaceStub($name)
    {
        return "<?php

declare(strict_types=1);

namespace App\\Contracts;

use JoeSu\\LaravelScaffold\\BaseServiceInterface;

interface {$name}ServiceInterface extends BaseServiceInterface
{
    // Add custom methods here
}
";
    }

    protected function getServiceClassStub($name)
    {
        return "<?php

declare(strict_types=1);

namespace App\\Services;

use App\\Contracts\\{$name}ServiceInterface;
use App\\Contracts\\{$name}RepositoryInterface;
use JoeSu\\LaravelScaffold\\BaseService;

class {$name}Service extends BaseService implements {$name}ServiceInterface
{
    public function __construct({$name}RepositoryInterface \$repository)
    {
        parent::__construct(\$repository);
    }

    // Add business logic methods here
}
";
    }

    protected function getControllerStub($name)
    {
        $lowerName = Str::lower($name);
        $pluralName = Str::plural($lowerName);

        $stub = <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\{name}ServiceInterface;
use App\Http\Requests\Store{name}Request;
use App\Http\Requests\Update{name}Request;
use App\Http\Requests\Index{name}Request;
use App\Http\Requests\Show{name}Request;
use Illuminate\Http\Request;

class {name}Controller extends Controller
{
    protected ${lowerName}Service;

    public function __construct({name}ServiceInterface ${lowerName}Service)
    {
        $this->{lowerName}Service = ${lowerName}Service;
    }

    /**
     * Display all {name}s (supports pagination, sorting, relationships, filtering)
     */
    public function index(Index{name}Request $request)
    {
        $perPage = $request->get('per_page', 0);
        $orderBy = $request->get('order_by');
        $orderDirection = $request->get('order_direction', 'asc');
        $relationships = $request->get('with', []);
        $columns = $request->get('columns', ['*']);
        $filters = $request->get('filters', []);

        ${pluralName} = $this->{lowerName}Service->index(
            $perPage,
            $orderBy,
            $orderDirection,
            $relationships,
            $columns,
            $filters
        );

        return response()->json(${pluralName});
    }

    /**
     * Display specific {name}
     */
    public function show(Show{name}Request $request, $id)
    {
        try {
            $columns = $request->get('columns', ['*']);
            $relationships = $request->get('with', []);
            ${lowerName} = $this->{lowerName}Service->find(
                $id, $columns, $relationships);
            return response()->json(${lowerName});
        } catch (\Exception $e) {
            return response()->json(['message' => '{name} not found'], 404);
        }
    }

    /**
     * Create new {name}
     */
    public function store(Store{name}Request $request)
    {
        ${lowerName} = $this->{lowerName}Service->create(
            $request->validated());
        return response()->json(${lowerName}, 201);
    }

    /**
     * Update {name}
     */
    public function update(Update{name}Request $request, $id)
    {
        ${lowerName} = $this->{lowerName}Service->update(
            $id, $request->validated());
        return response()->json(${lowerName});
    }

    /**
     * Delete {name}
     */
    public function destroy($id)
    {
        $this->{lowerName}Service->delete($id);
        return response()->json(['message' => '{name} deleted successfully']);
    }
}
PHP;
        return str_replace([
            '{name}',
            '{lowerName}',
            '{pluralName}'
        ], [
            $name,
            $lowerName,
            $pluralName
        ], $stub);
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
        $newBinding = "\n        \$this->app->bind(\\App\\Contracts\\{$name}ServiceInterface::class, \\App\\Services\\{$name}Service::class);\n";
        $newBinding .= "        \$this->app->bind(\\App\\Contracts\\{$name}RepositoryInterface::class, \\App\\Repositories\\{$name}Repository::class);\n";

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
