<?php

use Illuminate\Support\Facades\Artisan;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigGeneratorService;

it('displays success message when config is generated', function () {
    $mockService = Mockery::mock(IConfigGeneratorService::class);
    $mockService->shouldReceive('generate')
        ->once()
        ->with('mail', false)
        ->andReturn('App\\Config\\MailConfig');

    $this->app->instance(IConfigGeneratorService::class, $mockService);

    $this
        ->artisan('make:config mail')
        ->expectsOutput('OOP config created: App\\Config\\MailConfig')
        ->assertExitCode(0);
});

it('displays error message on failure', function () {
    $mockService = Mockery::mock(IConfigGeneratorService::class);
    $mockService->shouldReceive('generate')
        ->once()
        ->with('mail', false)
        ->andThrow(new RuntimeException('Config already exists'));

    $this->app->instance(IConfigGeneratorService::class, $mockService);

    $this
        ->artisan('make:config mail')
        ->expectsOutput('Config already exists')
        ->assertExitCode(1);
});

it('shows an error if the config key does not exist', function () {
    $mockService = Mockery::mock(IConfigGeneratorService::class);
    $mockService->shouldReceive('generate')
        ->once()
        ->with('nonexistent', false)
        ->andThrow(new RuntimeException("Config key 'nonexistent' is not defined or not an array."));

    $this->app->instance(IConfigGeneratorService::class, $mockService);

    $this
        ->artisan('make:config nonexistent')
        ->expectsOutput("Config key 'nonexistent' is not defined or not an array.")
        ->assertExitCode(1);
});

it('shows an error if the config file does not return an array', function () {
    $mockService = Mockery::mock(IConfigGeneratorService::class);
    $mockService->shouldReceive('generate')
        ->once()
        ->with('notarray', false)
        ->andThrow(new RuntimeException("Config key 'notarray' is not defined or not an array."));

    $this->app->instance(IConfigGeneratorService::class, $mockService);

    $this
        ->artisan('make:config notarray')
        ->expectsOutput("Config key 'notarray' is not defined or not an array.")
        ->assertExitCode(1);
});