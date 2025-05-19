<?php

namespace VeiligLanceren\LaravelOopConfig\Services;

use Illuminate\Contracts\Foundation\Application;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigRegistryService;

class ConfigRegistryService implements IConfigRegistryService
{
    /**
     * @param Application $app
     */
    public function __construct(
        protected Application $app
    ) {}

    /**
     * {@inheritDoc}
     */
    public function registerAll(string $path, string $namespace): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*Config.php') as $file) {
            $class = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class) && method_exists($class, 'fromConfig')) {
                $this->app->singleton($class, fn () => $class::fromConfig());
            }
        }
    }
}