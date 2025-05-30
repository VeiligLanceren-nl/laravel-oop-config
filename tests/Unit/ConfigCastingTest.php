<?php

use VeiligLanceren\LaravelOopConfig\Enums\MailStatus;
use VeiligLanceren\LaravelOopConfig\Config\MailConfig;

it('casts int, bool, float, string, array, enum, and url config values', function () {
    $config = [
        'host' => 'smtp.mail.com',
        'port' => '465',
        'encryption' => 'ssl',
        'status' => 'enabled',
        'endpoint' => 'https://api.mail.com',
        'options' => ['foo' => 'bar'],
        'custom' => 'foo',
    ];
    $mailConfig = new MailConfig($config);

    expect($mailConfig->host())->toBe('smtp.mail.com');
    expect($mailConfig->port())->toBeInt()->toBe(465);
    expect($mailConfig->encryption())->toBe('ssl');
    expect($mailConfig->status())->toBeInstanceOf(MailStatus::class);
    expect($mailConfig->endpoint())->toBe('https://api.mail.com');
    expect($mailConfig->options())->toBeArray()->toMatchArray(['foo' => 'bar']);
    expect($mailConfig->customValue())->toBe('foo');
});

it('throws for invalid int', function () {
    $config = ['port' => 'not_an_int'];
    $mailConfig = new MailConfig($config);
    $mailConfig->port();
})->throws(InvalidArgumentException::class);

it('throws for invalid enum', function () {
    $config = ['status' => 'not_valid'];
    $mailConfig = new MailConfig($config);
    $mailConfig->status();
})->throws(InvalidArgumentException::class);

it('throws for invalid url', function () {
    $config = ['endpoint' => 'not_a_url'];
    $mailConfig = new MailConfig($config);
    $mailConfig->endpoint();
})->throws(InvalidArgumentException::class);

it('throws for missing key', function () {
    $config = [];
    $mailConfig = new MailConfig($config);
    $mailConfig->host();
})->throws(InvalidArgumentException::class);

it('throws for custom validation', function () {
    $config = ['custom' => 'bar'];
    $mailConfig = new MailConfig($config);
    $mailConfig->customValue();
})->throws(InvalidArgumentException::class);