<?php

namespace VeiligLanceren\LaravelOopConfig\Services;

use RuntimeException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigClassGeneratorService;

class ConfigClassGeneratorService implements IConfigClassGeneratorService
{
    protected string $classStubPath;

    public function __construct(
        protected AccessorMethodGeneratorService $methodGenerator
    ) {
        $this->classStubPath = config('oop-config.stubs.class', __DIR__.'/../../stubs/oop-config-full.stub');
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $key, bool $force = false): string
    {
        $className = Str::studly($key) . 'Config';
        $namespace = config('oop-config.namespace', 'App\\Config');
        $path = config('oop-config.path', app_path('Config'));
        $fullPath = $path . '/' . $className . '.php';

        if (File::exists($fullPath) && !$force) {
            throw new RuntimeException("Config already exists: {$fullPath}");
        }

        $configArray = config($key);

        if (!is_array($configArray)) {
            throw new RuntimeException("Config key '{$key}' is not defined or not an array.");
        }

        $methods = $this->methodGenerator->generate($configArray);
        $stub = File::get($this->classStubPath);
        $content = str_replace(
            ['{{ namespace }}', '{{ className }}', '{{ configKey }}', '{{ methods }}'],
            [$namespace, $className, $key, $methods],
            $stub
        );

        File::put($fullPath, $content);

        return $namespace . '\\' . $className;
    }
}
