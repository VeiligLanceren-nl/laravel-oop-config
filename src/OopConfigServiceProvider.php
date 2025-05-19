<?php

namespace VeiligLanceren\LaravelOopConfig;

use Illuminate\Support\ServiceProvider;
use VeiligLanceren\LaravelOopConfig\Console\Commands\MakeConfigCommand;
use VeiligLanceren\LaravelOopConfig\Console\Commands\GenerateAllConfigsCommand;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\{IAccessorMethodGeneratorService,
    IConfigBatchGeneratorService,
    IConfigClassGeneratorService,
    IConfigGeneratorService,
    IConfigRegistryService};
use VeiligLanceren\LaravelOopConfig\Services\{
    AccessorMethodGeneratorService,
    ConfigBatchGeneratorService,
    ConfigClassGeneratorService,
    ConfigGeneratorService,
    ConfigRegistryService
};

class OopConfigServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/oop-config.php' => config_path('oop-config.php'),
        ], 'config');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/oop-config.php', 'oop-config');

        $this->app->singleton(IAccessorMethodGeneratorService::class, AccessorMethodGeneratorService::class);
        $this->app->singleton(IConfigBatchGeneratorService::class, ConfigBatchGeneratorService::class);
        $this->app->singleton(IConfigClassGeneratorService::class, ConfigClassGeneratorService::class);
        $this->app->singleton(IConfigGeneratorService::class, ConfigGeneratorService::class);
        $this->app->singleton(IConfigRegistryService::class, ConfigRegistryService::class);

        $this->commands([
            MakeConfigCommand::class,
            GenerateAllConfigsCommand::class,
        ]);

        $this->app->afterResolving(IConfigRegistryService::class, function (ConfigRegistryService $service) {
            if (config('oop-config.autoload', true)) {
                $service->registerAll(
                    config('oop-config.path'),
                    config('oop-config.namespace')
                );
            }
        });
    }
}
