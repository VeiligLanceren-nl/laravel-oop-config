<?php

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use VeiligLanceren\LaravelOopConfig\Services\AccessorMethodGeneratorService;
use VeiligLanceren\LaravelOopConfig\Services\ConfigClassGeneratorService;

beforeEach(function () {
    File::swap(new Filesystem());
    config()->set('oop-config.namespace', 'App\\Config');
    config()->set('oop-config.path', base_path('app/Config'));
});

it('generates a class file for a valid config', function () {
    File::shouldReceive('exists')->once()->andReturn(false);
    File::shouldReceive('get')->once()->andReturn('namespace {{ namespace }}; class {{ className }} { {{ methods }} }');
    File::shouldReceive('put')->once()->withArgs(function ($path, $content) {
        return str_contains($path, 'MailConfig.php') && str_contains($content, 'class MailConfig');
    });

    config()->set('mail', ['host' => 'smtp.example.com']);

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $mockMethodGen->shouldReceive('generate')->once()->andReturn("// generated methods");

    $service = new ConfigClassGeneratorService($mockMethodGen);
    $class = $service->generate('mail');

    expect($class)->toBe('App\\Config\\MailConfig');
});

it('throws if config already exists and not forced', function () {
    File::shouldReceive('exists')->once()->andReturn(true);

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $service = new ConfigClassGeneratorService($mockMethodGen);

    $service->generate('mail');
})->throws(RuntimeException::class, 'Config already exists');

it('throws if config key is not defined or not an array', function () {
    File::shouldReceive('exists')->once()->andReturn(false);

    config()->set('not_an_array', 'invalid');

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $service = new ConfigClassGeneratorService($mockMethodGen);

    $service->generate('not_an_array');
})->throws(RuntimeException::class, "Config key 'not_an_array' is not defined or not an array.");
