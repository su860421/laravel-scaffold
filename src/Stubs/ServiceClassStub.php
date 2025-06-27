<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class ServiceClassStub
{
    public static function generate(string $name): string
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
}
