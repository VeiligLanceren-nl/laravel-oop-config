<?php

namespace VeiligLanceren\LaravelOopConfig\Config;

use VeiligLanceren\LaravelOopConfig\AbstractConfig;
use VeiligLanceren\LaravelOopConfig\Enums\MailStatus;

class MailConfig extends AbstractConfig
{
    public function host(): string
    {
        return $this->get('host', 'string');
    }

    public function port(): int
    {
        return $this->get('port', 'int');
    }

    public function encryption(): string
    {
        return $this->get('encryption', 'string');
    }

    public function status(): MailStatus
    {
        return $this->get('status', 'enum:' . MailStatus::class);
    }

    public function endpoint(): string
    {
        return $this->get('endpoint', 'url');
    }

    public function options(): array
    {
        return $this->get('options', 'array');
    }

    // Example of a custom cast/validation
    public function customValue()
    {
        return $this->get('custom', 'custom', [
            'callback' => function($value) {
                // custom validation logic
                if ($value !== 'foo') throw new \InvalidArgumentException('Custom value must be "foo"');
                return $value;
            }
        ]);
    }
}