<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class ServiceInterfaceStub
{
    public static function generate(string $name): string
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
}
