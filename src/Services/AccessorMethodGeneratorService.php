<?php

namespace VeiligLanceren\LaravelOopConfig\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use VeiligLanceren\LaravelOopConfig\Interfaces\Services\IAccessorMethodGeneratorService;

class AccessorMethodGeneratorService implements IAccessorMethodGeneratorService
{
    /**
     * @var string
     */
    protected string $methodStubPath;

    public function __construct()
    {
        $this->methodStubPath = __DIR__ . '/../../stubs/oop-config-method.stub';
    }

    /**
     * {@inheritDoc}
     */
    public function generate(array $config, string $prefix = '', string $path = ''): string
    {
        $output = '';
        $methodStub = File::get($this->methodStubPath);

        foreach ($config as $key => $value) {
            $fullKey = $prefix . $key;
            $methodName = Str::camel(Str::studly(str_replace('.', '_', $fullKey)));
            $returnType = $this->guessType($value);
            $returnValue = $this->buildReturnExpression($fullKey);

            if (!is_array($value)) {
                $method = str_replace(
                    ['{{ methodName }}', '{{ returnType }}', '{{ returnValue }}'],
                    [$methodName, $returnType, $returnValue],
                    $methodStub
                );

                $output .= $method;
            }

            if (is_array($value)) {
                $output .= $this->generate($value, $prefix . $key . '.');
            }
        }

        return $output;
    }

    /**
     * @param mixed $value
     * @return string
     */
    protected function guessType(mixed $value): string
    {
        return match (true) {
            is_string($value) => 'string',
            is_int($value) => 'int',
            is_bool($value) => 'bool',
            is_float($value) => 'float',
            is_array($value) => 'array',
            default => 'mixed',
        };
    }

    /**
     * @param string $keyPath
     * @return string
     */
    protected function buildReturnExpression(string $keyPath): string
    {
        $parts = explode('.', $keyPath);
        $code = '$this->config';

        foreach ($parts as $part) {
            $code .= "['$part']";
        }

        return $code . ';';
    }
}
