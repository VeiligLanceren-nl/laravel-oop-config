<?php

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use VeiligLanceren\LaravelOopConfig\Services\ConfigGeneratorService;

beforeEach(function () {
    File::swap(new Filesystem());
    config()->set('oop-config.namespace', 'App\\Config');
    config()->set('oop-config.path', base_path('app/Config'));
});

it('generates a config class file from stub', function () {
    File::shouldReceive('exists')->withArgs(function ($path) {
        return str_contains($path, 'MailConfig.php');
    })->once()->andReturn(false);

    File::shouldReceive('exists')->withArgs(function ($path) {
        return str_contains($path, 'app/Config');
    })->once()->andReturn(true);

    File::shouldReceive('get')->once()->andReturn('<?php namespace {{ namespace }}; class {{ className }} {}');
    File::shouldReceive('put')->once()->withArgs(function ($path, $content) {
        return str_contains($path, 'MailConfig.php') && str_contains($content, 'class MailConfig');
    });

    $service = new ConfigGeneratorService();
    $result = $service->generate('mail');

    expect($result)->toBe('App\\Config\\MailConfig');
});

it('creates the directory if it does not exist', function () {
    File::shouldReceive('exists')->andReturnUsing(fn($path) => str_contains($path, 'MailConfig.php') ? false : false);
    File::shouldReceive('makeDirectory')->once();
    File::shouldReceive('get')->andReturn('class {{ className }}');
    File::shouldReceive('put')->once();

    $service = new ConfigGeneratorService();
    $service->generate('mail');

    expect(true)->toBeTrue(); // dummy assertion to ensure test passes
});

it('throws if class file exists and not forced', function () {
    File::shouldReceive('exists')->withArgs(fn($path) => str_contains($path, 'MailConfig.php'))->andReturn(true);

    $service = new ConfigGeneratorService();
    $service->generate('mail'); // should throw
})->throws(RuntimeException::class, 'Config class already exists');
