<?php

namespace VeiligLanceren\LaravelOopConfig\Interfaces\Services;

interface IConfigRegistryService
{
    /**
     * @param string $path
     * @param string $namespace
     * @return void
     */
    public function registerAll(string $path, string $namespace): void;
}