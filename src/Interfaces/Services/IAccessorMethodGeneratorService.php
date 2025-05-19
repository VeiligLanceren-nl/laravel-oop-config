<?php

namespace VeiligLanceren\LaravelOopConfig\Interfaces\Services;

interface IAccessorMethodGeneratorService
{
    /**
     * @param array $config
     * @param string $prefix
     * @return string
     */
    public function generate(array $config, string $prefix = ''): string;
}