<?php

use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelOopConfig\Services\AccessorMethodGeneratorService;

beforeEach(function () {
    File::shouldReceive('get')
        ->andReturn("
    public function {{ methodName }}(): {{ returnType }}
    {
        return {{ returnValue }};
    }
");
});

it('generates simple accessors correctly', function () {
    $generator = new AccessorMethodGeneratorService();

    $config = [
        'host' => 'smtp.example.com',
        'port' => 587,
        'ssl' => true,
    ];

    $methods = $generator->generate($config);

    expect($methods)->toContain('public function host(): string');
    expect($methods)->toContain("return \$this->config['host'];");

    expect($methods)->toContain('public function port(): int');
    expect($methods)->toContain("return \$this->config['port'];");

    expect($methods)->toContain('public function ssl(): bool');
    expect($methods)->toContain("return \$this->config['ssl'];");
});

it('recursively generates accessors for nested config arrays', function () {
    $generator = new AccessorMethodGeneratorService();

    $config = [
        'from' => [
            'address' => 'info@example.com',
        ],
    ];

    $methods = $generator->generate($config);

    expect($methods)->toContain('public function fromAddress(): string');
    expect($methods)->toContain("return \$this->config['from']['address'];");
});

