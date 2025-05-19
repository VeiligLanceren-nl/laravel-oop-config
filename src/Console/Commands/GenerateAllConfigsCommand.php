<?php

namespace VeiligLanceren\LaravelOopConfig\Console\Commands;

use Illuminate\Console\Command;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigBatchGeneratorService;

class GenerateAllConfigsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'config:generate-all {--force}';

    /**
     * @var string
     */
    protected $description = 'Generate OOP config classes for all Laravel config files';

    /**
     * @param IConfigBatchGeneratorService $generator
     * @return int
     */
    public function handle(IConfigBatchGeneratorService $generator): int
    {
        $results = $generator->generateAll($this->option('force'));

        foreach ($results as $result) {
            if (isset($result['generated'])) {
                $this->info('Generated: ' . $result['generated']);
            } elseif (isset($result['skipped'])) {
                $this->warn('Skipped existing: ' . $result['skipped']);
            }
        }

        return self::SUCCESS;
    }
}