<?php

namespace VeiligLanceren\LaravelOopConfig\Interfaces\Services;

interface IConfigBatchGeneratorService
{
    /**
     * @param bool $force
     * @return array
     */
    public function generateAll(bool $force = false): array;


}