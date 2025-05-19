<?php

namespace VeiligLanceren\LaravelOopConfig\Services;

use RuntimeException;
use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigBatchGeneratorService;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigClassGeneratorService;

class ConfigBatchGeneratorService implements IConfigBatchGeneratorService
{
    /**
     * @param IConfigClassGeneratorService $classGenerator
     */
    public function __construct(
        protected IConfigClassGeneratorService $classGenerator
    ) {}

    /**
     * {@inheritDoc}
     */
    public function generateAll(bool $force = false): array
    {
        $generated = [];
        $configDir = config_path();
        $outputDir = config('oop-config.path', app_path('Config'));

        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        foreach (File::files($configDir) as $file) {
            if ($file->getExtension() !== 'php') continue;

            $configKey = $file->getFilenameWithoutExtension();

            try {
                $this->classGenerator->generate($configKey, $force);
                $generated[] = ['generated' => $configKey];
            } catch (RuntimeException $e) {
                $generated[] = ['skipped' => $configKey];
            }
        }

        return $generated;
    }
}
