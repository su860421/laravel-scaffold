<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Testing\PendingCommand;

class RegisterApiRoutesTest extends TestCase
{
    private const WEB_LINE = "web: __DIR__.'/../routes/web.php',";
    private const API_LINE = "api: __DIR__.'/../routes/api.php',";
    private const COMMANDS_LINE = "commands: __DIR__.'/../routes/console.php',";

    private string $appRoot;

    protected function setUp(): void
    {
        parent::setUp();

        // 使用獨立的暫存專案目錄,避免改到 Testbench 骨架的 bootstrap/app.php
        $this->appRoot = sys_get_temp_dir() . '/laravel-scaffold-routes-' . uniqid();

        foreach (['app/Models', 'app/Http/Controllers', 'app/Providers', 'bootstrap', 'routes'] as $directory) {
            File::makeDirectory("{$this->appRoot}/{$directory}", 0755, true);
        }

        File::put(
            "{$this->appRoot}/app/Providers/AppServiceProvider.php",
            "<?php\n\nnamespace App\\Providers;\n\nuse Illuminate\\Support\\ServiceProvider;\n\n"
                . "class AppServiceProvider extends ServiceProvider\n{\n"
                . "    public function register(): void\n    {\n        //\n    }\n}\n"
        );

        $this->app->setBasePath($this->appRoot);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->appRoot);

        parent::tearDown();
    }

    public function testRegistersApiRoutesAfterWebRouteInStandardSkeleton()
    {
        $this->writeBootstrapFile($this->bootstrapWithRouting([
            self::WEB_LINE,
            self::COMMANDS_LINE,
            "health: '/up',",
        ]));

        $this->generator()->run();

        $content = $this->readBootstrapFile();
        $this->assertStringContainsString(
            self::WEB_LINE . PHP_EOL . '        ' . self::API_LINE,
            $content
        );
        $this->assertSame(1, substr_count($content, self::API_LINE));
    }

    public function testUncommentsExistingApiRouteLine()
    {
        $this->writeBootstrapFile($this->bootstrapWithRouting([
            self::WEB_LINE,
            '// ' . self::API_LINE,
            self::COMMANDS_LINE,
        ]));

        $this->generator()->run();

        $content = $this->readBootstrapFile();
        $this->assertStringNotContainsString('// api: ', $content);
        $this->assertSame(1, substr_count($content, self::API_LINE));
    }

    public function testWarnsAndLeavesFileUntouchedWhenRoutingIsCustom()
    {
        $original = $this->bootstrapWithRouting([
            'using: function () {',
            '    //',
            '},',
        ]);
        $this->writeBootstrapFile($original);

        $this->generator()
            ->expectsOutputToContain('routes/api.php is not registered')
            ->run();

        $this->assertSame($original, $this->readBootstrapFile());
    }

    public function testDoesNotDuplicateAlreadyRegisteredApiRoute()
    {
        $original = $this->bootstrapWithRouting([
            self::WEB_LINE,
            self::API_LINE,
            self::COMMANDS_LINE,
        ]);
        $this->writeBootstrapFile($original);

        $this->generator()->run();

        $this->assertSame($original, $this->readBootstrapFile());
    }

    public function testLeavesLegacyBootstrapFileUntouched()
    {
        $original = "<?php\n\n\$app = new Illuminate\\Foundation\\Application(dirname(__DIR__));\n\nreturn \$app;\n";
        $this->writeBootstrapFile($original);

        $this->generator()->run();

        $this->assertSame($original, $this->readBootstrapFile());
    }

    public function testSucceedsWhenBootstrapFileIsMissing()
    {
        $this->generator()->run();

        $this->assertFileDoesNotExist($this->bootstrapPath());
        $this->assertFileExists("{$this->appRoot}/routes/api.php");
    }

    private function generator(): PendingCommand
    {
        return $this->artisan('make:repository', [
            'name' => 'Product',
            '--force' => true,
        ])->assertExitCode(0);
    }

    private function bootstrapWithRouting(array $routingLines): string
    {
        $routing = implode("\n", array_map(
            fn (string $line): string => "        {$line}",
            $routingLines
        ));

        return "<?php\n\nuse Illuminate\\Foundation\\Application;\n\n"
            . "return Application::configure(basePath: dirname(__DIR__))\n"
            . "    ->withRouting(\n{$routing}\n    )\n    ->create();\n";
    }

    private function bootstrapPath(): string
    {
        return "{$this->appRoot}/bootstrap/app.php";
    }

    private function writeBootstrapFile(string $content): void
    {
        File::put($this->bootstrapPath(), $content);
    }

    private function readBootstrapFile(): string
    {
        return File::get($this->bootstrapPath());
    }
}
