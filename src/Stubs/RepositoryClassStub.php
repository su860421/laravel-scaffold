<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class RepositoryClassStub
{
    public static function generate(string $name, string $model): string
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
}
