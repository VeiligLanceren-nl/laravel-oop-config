<?php

use Illuminate\Support\Facades\File;
use Illuminate\Filesystem\Filesystem;
use VeiligLanceren\LaravelOopConfig\Services\AccessorMethodGeneratorService;
use VeiligLanceren\LaravelOopConfig\Services\ConfigClassGeneratorService;

beforeEach(function () {
    config()->set('oop-config.namespace', 'App\\Config');
    config()->set('oop-config.path', base_path('app/Config'));
    config()->set('oop-config.stubs.class', base_path('stubs/class.stub'));
});

it('generates a class file for a valid config', function () {
    // Prepare for directory and file existence
    File::shouldReceive('exists')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('isWritable')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('exists')->with(base_path('app/Config/MailConfig.php'))->once()->andReturn(false);
    File::shouldReceive('exists')->with(base_path('stubs/class.stub'))->once()->andReturn(true);
    File::shouldReceive('get')->with(base_path('stubs/class.stub'))->once()->andReturn('namespace {{ namespace }}; class {{ className }} { {{ methods }} }');
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
    File::shouldReceive('exists')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('isWritable')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('exists')->with(base_path('app/Config/MailConfig.php'))->once()->andReturn(true);

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $service = new ConfigClassGeneratorService($mockMethodGen);

    $service->generate('mail');
})->throws(RuntimeException::class, 'Config already exists');

it('throws if config key is not defined or not an array', function () {
    File::shouldReceive('exists')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('isWritable')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('exists')->with(base_path('app/Config/NotAnArrayConfig.php'))->once()->andReturn(false);

    config()->set('not_an_array', 'invalid');

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $service = new ConfigClassGeneratorService($mockMethodGen);

    $service->generate('not_an_array');
})->throws(RuntimeException::class, "Config key 'not_an_array' is not defined or not an array.");

it('throws if output directory is not writable', function () {
    // Simulate directory does not exist and cannot be created
    File::shouldReceive('exists')->with(base_path('app/Config'))->once()->andReturn(false);
    File::shouldReceive('makeDirectory')->with(base_path('app/Config'), 0755, true)->once()->andReturn(false);

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $mockMethodGen->shouldNotReceive('generate');
    $service = new ConfigClassGeneratorService($mockMethodGen);

    expect(fn() => $service->generate('mail'))->toThrow('Output directory is not writable');
});

it('throws if custom stub file does not exist', function () {
    // Directory exists and is writable
    File::shouldReceive('exists')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('isWritable')->with(base_path('app/Config'))->once()->andReturn(true);
    File::shouldReceive('exists')->with(base_path('app/Config/MailConfig.php'))->once()->andReturn(false);
    File::shouldReceive('exists')->with(base_path('stubs/nonexistent.stub'))->once()->andReturn(false);

    $mockMethodGen = Mockery::mock(AccessorMethodGeneratorService::class);
    $mockMethodGen->shouldNotReceive('generate');
    config()->set('oop-config.stubs.class', base_path('stubs/nonexistent.stub'));
    $service = new ConfigClassGeneratorService($mockMethodGen);

    expect(fn() => $service->generate('mail'))->toThrow('Stub file does not exist');
});