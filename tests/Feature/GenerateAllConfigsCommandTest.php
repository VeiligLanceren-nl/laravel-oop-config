<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigBatchGeneratorService;

beforeEach(function () {
    File::swap(new Filesystem());
});

it('displays generated and skipped messages', function () {
    $mockService = Mockery::mock(IConfigBatchGeneratorService::class);
    $mockService->shouldReceive('generateAll')
        ->once()
        ->andReturn([
            ['generated' => 'mail'],
            ['skipped' => 'database'],
        ]);

    $this->app->instance(IConfigBatchGeneratorService::class, $mockService);

    $this
        ->artisan('config:generate-all')
        ->expectsOutput('Generated: mail')
        ->expectsOutput('Skipped existing: database')
        ->assertExitCode(0);
});

it('runs successfully with --force option', function () {
    $mockService = Mockery::mock(IConfigBatchGeneratorService::class);
    $mockService->shouldReceive('generateAll')
        ->once()
        ->with(true)
        ->andReturn([]);

    $this->app->instance(IConfigBatchGeneratorService::class, $mockService);

    Artisan::call('config:generate-all', ['--force' => true]);

    expect(Artisan::output())->toBe('');
});
