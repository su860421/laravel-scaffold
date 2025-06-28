<?php

namespace Tests\Stubs;

use Tests\TestCase;
use JoeSu\LaravelScaffold\Stubs\StubManager;

class StubManagerTest extends TestCase
{
    protected $testName = 'TestUser';
    protected $testModel = 'TestUser';

    public function testGenerateModel()
    {
        $stub = StubManager::generateModel($this->testName, $this->testModel);

        $this->assertStringContainsString('class ' . $this->testModel . ' extends Model', $stub);
        $this->assertStringContainsString('use HasFactory;', $stub);
        $this->assertStringContainsString('protected $fillable', $stub);
        $this->assertStringContainsString('protected $casts', $stub);
        $this->assertStringContainsString('namespace App\\Models;', $stub);
    }

    public function testGenerateRequest()
    {
        $types = ['Store', 'Update', 'Index', 'Show'];

        foreach ($types as $type) {
            $stub = StubManager::generateRequest($this->testName, $type);
            $requestName = "{$type}{$this->testName}Request";

            $this->assertStringContainsString("class {$requestName} extends FormRequest", $stub);
            $this->assertStringContainsString('namespace App\\Http\\Requests\\' . $this->testName . ';', $stub);
            $this->assertStringContainsString('public function authorize(): bool', $stub);
            $this->assertStringContainsString('public function rules(): array', $stub);
        }
    }

    public function testGenerateRequestWithIndexType()
    {
        $stub = StubManager::generateRequest($this->testName, 'Index');

        $this->assertStringContainsString("'per_page' => ['integer', 'min:1', 'max:100']", $stub);
        $this->assertStringContainsString("'order_by' => ['string']", $stub);
        $this->assertStringContainsString("'order_direction' => ['in:asc,desc']", $stub);
        $this->assertStringContainsString("'with' => ['array']", $stub);
        $this->assertStringContainsString("'columns' => ['array']", $stub);
        $this->assertStringContainsString("'filters' => ['array']", $stub);
    }

    public function testGenerateRequestWithShowType()
    {
        $stub = StubManager::generateRequest($this->testName, 'Show');

        $this->assertStringContainsString("'columns' => ['array']", $stub);
        $this->assertStringContainsString("'with' => ['array']", $stub);
    }

    public function testGenerateRepositoryInterface()
    {
        $stub = StubManager::generateRepositoryInterface($this->testName);

        $this->assertStringContainsString("interface {$this->testName}RepositoryInterface extends BaseRepositoryInterface", $stub);
        $this->assertStringContainsString('namespace App\\Contracts;', $stub);
        $this->assertStringContainsString('use JoeSu\\LaravelScaffold\\BaseRepositoryInterface;', $stub);
    }

    public function testGenerateRepositoryClass()
    {
        $stub = StubManager::generateRepositoryClass($this->testName, $this->testModel);

        $this->assertStringContainsString("class {$this->testName}Repository extends BaseRepository", $stub);
        $this->assertStringContainsString("implements {$this->testName}RepositoryInterface", $stub);
        $this->assertStringContainsString('namespace App\\Repositories;', $stub);
        $this->assertStringContainsString("use App\\Models\\{$this->testModel};", $stub);
        $this->assertStringContainsString("use JoeSu\\LaravelScaffold\\BaseRepository;", $stub);
        $this->assertStringContainsString("public function __construct({$this->testModel} \$model)", $stub);
    }

    public function testGenerateServiceInterface()
    {
        $stub = StubManager::generateServiceInterface($this->testName);

        $this->assertStringContainsString("interface {$this->testName}ServiceInterface extends BaseServiceInterface", $stub);
        $this->assertStringContainsString('namespace App\\Contracts;', $stub);
        $this->assertStringContainsString('use JoeSu\\LaravelScaffold\\BaseServiceInterface;', $stub);
    }

    public function testGenerateServiceClass()
    {
        $stub = StubManager::generateServiceClass($this->testName);

        $this->assertStringContainsString("class {$this->testName}Service extends BaseService", $stub);
        $this->assertStringContainsString("implements {$this->testName}ServiceInterface", $stub);
        $this->assertStringContainsString('namespace App\\Services;', $stub);
        $this->assertStringContainsString("use App\\Contracts\\{$this->testName}RepositoryInterface;", $stub);
        $this->assertStringContainsString("use JoeSu\\LaravelScaffold\\BaseService;", $stub);
        $this->assertStringContainsString("public function __construct({$this->testName}RepositoryInterface \$repository)", $stub);
    }

    public function testGenerateController()
    {
        $stub = StubManager::generateController($this->testName);

        $this->assertStringContainsString("class {$this->testName}Controller extends Controller", $stub);
        $this->assertStringContainsString('namespace App\\Http\\Controllers;', $stub);
        $this->assertStringContainsString("use App\\Contracts\\{$this->testName}ServiceInterface;", $stub);
        $this->assertStringContainsString('public function index(', $stub);
        $this->assertStringContainsString('public function show(', $stub);
        $this->assertStringContainsString('public function store(', $stub);
        $this->assertStringContainsString('public function update(', $stub);
        $this->assertStringContainsString('public function destroy(', $stub);
    }
}
