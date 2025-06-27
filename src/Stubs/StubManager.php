<?php

declare(strict_types=1);

namespace JoeSu\LaravelScaffold\Stubs;

class StubManager
{
    public static function generateModel(string $name, string $model): string
    {
        return ModelStub::generate($name, $model);
    }

    public static function generateRequest(string $name, string $type): string
    {
        return RequestStub::generate($name, $type);
    }

    public static function generateRepositoryInterface(string $name): string
    {
        return RepositoryInterfaceStub::generate($name);
    }

    public static function generateRepositoryClass(string $name, string $model): string
    {
        return RepositoryClassStub::generate($name, $model);
    }

    public static function generateServiceInterface(string $name): string
    {
        return ServiceInterfaceStub::generate($name);
    }

    public static function generateServiceClass(string $name): string
    {
        return ServiceClassStub::generate($name);
    }

    public static function generateController(string $name): string
    {
        return ControllerStub::generate($name);
    }
}
