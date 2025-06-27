<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \JoeSu\LaravelScaffold\Providers\LaravelScaffoldServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // 設定測試環境
        $app['config']->set('app.debug', true);

        // 確保命令在測試環境中可用
        $app['config']->set('app.env', 'testing');
    }

    protected function setUp(): void
    {
        parent::setUp();

        // 覆蓋 app_path 和 base_path 函數
        $this->overridePathFunctions();
    }

    protected function overridePathFunctions()
    {
        // 只有在函數不存在時才定義
        if (!function_exists('app_path')) {
            function app_path($path = '')
            {
                return sys_get_temp_dir() . '/laravel-scaffold-test/app' . ($path ? '/' . $path : '');
            }
        }

        if (!function_exists('base_path')) {
            function base_path($path = '')
            {
                return sys_get_temp_dir() . '/laravel-scaffold-test' . ($path ? '/' . $path : '');
            }
        }
    }
}
