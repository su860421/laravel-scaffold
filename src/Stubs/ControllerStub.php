<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

use Illuminate\Support\Str;

class ControllerStub
{
    public static function generate(string $name): string
    {
        $lowerName = Str::lower($name);
        $pluralName = Str::plural($lowerName);

        return "<?php

declare(strict_types=1);

namespace App\\Http\\Controllers;

use App\\Contracts\\{$name}ServiceInterface;
use App\\Http\\Requests\\Store{$name}Request;
use App\\Http\\Requests\\Update{$name}Request;
use App\\Http\\Requests\\Index{$name}Request;
use App\\Http\\Requests\\Show{$name}Request;
use Illuminate\\Http\\Request;

class {$name}Controller extends Controller
{
    protected \${$lowerName}Service;

    public function __construct({$name}ServiceInterface \${$lowerName}Service)
    {
        \$this->{$lowerName}Service = \${$lowerName}Service;
    }

    /**
     * Display all {$name}s (supports pagination, sorting, relationships, filtering)
     */
    public function index(Index{$name}Request \$request)
    {
        \$perPage = \$request->get('per_page', 0);
        \$orderBy = \$request->get('order_by');
        \$orderDirection = \$request->get('order_direction', 'asc');
        \$relationships = \$request->get('with', []);
        \$columns = \$request->get('columns', ['*']);
        \$filters = \$request->get('filters', []);

        \${$pluralName} = \$this->{$lowerName}Service->index(
            \$perPage,
            \$orderBy,
            \$orderDirection,
            \$relationships,
            \$columns,
            \$filters
        );

        return response()->json(\${$pluralName});
    }

    /**
     * Display specific {$name}
     */
    public function show(Show{$name}Request \$request, \$id)
    {
        try {
            \$columns = \$request->get('columns', ['*']);
            \$relationships = \$request->get('with', []);
            \${$lowerName} = \$this->{$lowerName}Service->find(
                \$id, \$columns, \$relationships);
            return response()->json(\${$lowerName});
        } catch (\\Exception \$e) {
            return response()->json(['message' => '{$name} not found'], 404);
        }
    }

    /**
     * Create new {$name}
     */
    public function store(Store{$name}Request \$request)
    {
        \${$lowerName} = \$this->{$lowerName}Service->create(
            \$request->validated());
        return response()->json(\${$lowerName}, 201);
    }

    /**
     * Update {$name}
     */
    public function update(Update{$name}Request \$request, \$id)
    {
        \${$lowerName} = \$this->{$lowerName}Service->update(
            \$id, \$request->validated());
        return response()->json(\${$lowerName});
    }

    /**
     * Delete {$name}
     */
    public function destroy(\$id)
    {
        \$this->{$lowerName}Service->delete(\$id);
        return response()->json(['message' => '{$name} deleted successfully']);
    }
}
";
    }
}
