<?php

namespace VeiligLanceren\LaravelOopConfig\Interfaces\Services;

interface IConfigGeneratorService
{
    /**
     * @param string $key
     * @param bool $force
     * @return string
     */
    public function generate(string $key, bool $force = false): string;
}