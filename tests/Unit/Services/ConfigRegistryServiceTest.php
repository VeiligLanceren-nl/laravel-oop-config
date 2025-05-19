<?php

use Illuminate\Contracts\Foundation\Application;
use VeiligLanceren\LaravelOopConfig\Services\ConfigRegistryService;

it('registers all valid config classes in the container', function () {
    $app = Mockery::mock(Application::class);
    $service = new ConfigRegistryService($app);

    $namespace = 'App\\Config';
    $path = __DIR__ . '/fake-config';

    // Set up a fake config class file
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    $className = 'TestConfig';
    $classPath = $path . '/' . $className . '.php';

    file_put_contents($classPath, "<?php namespace {$namespace}; class {$className} { public static function fromConfig() { return new static(); } }");

    require_once $classPath;

    $app->shouldReceive('singleton')
        ->once()
        ->with($namespace . '\\' . $className, Mockery::type('Closure'));

    $service->registerAll($path, $namespace);

    unlink($classPath);
    rmdir($path);
});
