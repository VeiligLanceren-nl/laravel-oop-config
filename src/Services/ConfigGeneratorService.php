<?php

namespace VeiligLanceren\LaravelOopConfig\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigGeneratorService;

class ConfigGeneratorService implements IConfigGeneratorService
{
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
            throw new \RuntimeException("Config class already exists at: {$fullPath}");
        }

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $stub = File::get(__DIR__ . '/../../stubs/oop-config.stub');
        $stub = str_replace(
            ['{{ className }}', '{{ namespace }}', '{{ configKey }}'],
            [$className, $namespace, $key],
            $stub
        );

        File::put($fullPath, $stub);

        return $namespace . '\\' . $className;
    }
}