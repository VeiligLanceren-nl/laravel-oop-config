<?php

namespace VeiligLanceren\LaravelOopConfig\Console\Commands;

use Illuminate\Console\Command;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigGeneratorService;

class MakeConfigCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'make:config {key} {--force}';

    /**
     * @var string
     */
    protected $description = 'Generate an OOP wrapper class for a config key';

    /**
     * @param IConfigGeneratorService $generator
     * @return int
     */
    public function handle(IConfigGeneratorService $generator): int
    {
        $key = $this->argument('key');

        try {
            $class = $generator->generate($key, $this->option('force'));
            $this->info("OOP config created: {$class}");
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}