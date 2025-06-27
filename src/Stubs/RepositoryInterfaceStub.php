<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class RepositoryInterfaceStub
{
    public static function generate(string $name): string
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
}
