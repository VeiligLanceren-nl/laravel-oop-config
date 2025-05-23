<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IConfigGeneratorService;

// Edge case: Custom stub is used for generation
it('uses custom stub when generating config class', function () {
    $customStub = "<?php\n// Custom stub content\n";
    File::shouldReceive('exists')->andReturn(false);
    File::shouldReceive('get')->with(resource_path('stubs/custom.stub'))->andReturn($customStub);

    config()->set('oop-config.stubs.class', resource_path('stubs/custom.stub'));

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