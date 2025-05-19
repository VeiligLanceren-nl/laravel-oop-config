<?php

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use VeiligLanceren\LaravelOopConfig\Services\ConfigBatchGeneratorService;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigClassGeneratorService;

beforeEach(function () {
    File::swap(new Filesystem()); // resets mock between tests
});

it('creates the output directory if it does not exist', function () {
    $mockClassGenerator = Mockery::mock(IConfigClassGeneratorService::class);
    $mockClassGenerator->shouldReceive('generate')->once()->andReturn('App\\Config\\MailConfig');

    File::shouldReceive('exists')
        ->withArgs(function ($path) {
            return str_contains($path, 'Config');
        })
        ->once()
        ->andReturn(false);

    File::shouldReceive('makeDirectory')->once();

    File::shouldReceive('files')->once()->andReturn([
        new class {
            public function getExtension() { return 'php'; }
            public function getFilenameWithoutExtension() { return 'mail'; }
        },
    ]);

    $service = new ConfigBatchGeneratorService($mockClassGenerator);

    $result = $service->generateAll();

    expect($result)->toBe([['generated' => 'mail']]);
});

it('skips non-php files', function () {
    $mockClassGenerator = Mockery::mock(IConfigClassGeneratorService::class);
    $mockClassGenerator->shouldNotReceive('generate');

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('files')->andReturn([
        new class {
            public function getExtension() { return 'txt'; }
            public function getFilenameWithoutExtension() { return 'readme'; }
        },
    ]);

    $service = new ConfigBatchGeneratorService($mockClassGenerator);

    $result = $service->generateAll();

    expect($result)->toBe([]);
});

it('handles existing config gracefully by skipping', function () {
    $mockClassGenerator = Mockery::mock(IConfigClassGeneratorService::class);
    $mockClassGenerator->shouldReceive('generate')->once()->andThrow(new RuntimeException());

    File::shouldReceive('exists')->andReturn(true);
    File::shouldReceive('files')->andReturn([
        new class {
            public function getExtension() { return 'php'; }
            public function getFilenameWithoutExtension() { return 'mail'; }
        },
    ]);

    $service = new ConfigBatchGeneratorService($mockClassGenerator);

    $result = $service->generateAll();

    expect($result)->toBe([['skipped' => 'mail']]);
});
