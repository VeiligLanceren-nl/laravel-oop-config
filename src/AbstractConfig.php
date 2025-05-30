<?php

namespace VeiligLanceren\LaravelOopConfig;

use InvalidArgumentException;
use UnitEnum;

abstract class AbstractConfig
{
    /**
     * @var array
     */
    protected array $data;

    /**
     * @param array $data The config array to wrap.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get a raw config value.
     */
    protected function getRaw(string $key)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new InvalidArgumentException("Config key '{$key}' does not exist.");
        }
        return $this->data[$key];
    }

    /**
     * Get a config value and cast/validate it.
     *
     * @param string $key
     * @param string $type Allowed: int, float, bool, string, array, url, custom
     * @param array $options Optional extra options for validation/casting (e.g. enumClass, customCallback)
     * @return mixed
     */
    protected function get(string $key, string $type = 'string', array $options = [])
    {
        $value = $this->getRaw($key);

        if (str_starts_with($type, 'enum:')) {
            $enumClass = substr($type, 5);
            return $this->castEnum($value, $enumClass);
        }

        switch ($type) {
            case 'int':
                return $this->castInt($value);
            case 'float':
                return $this->castFloat($value);
            case 'bool':
                return $this->castBool($value);
            case 'string':
                return $this->castString($value);
            case 'array':
                return $this->castArray($value);
            case 'url':
                return $this->castUrl($value);
            case 'custom':
                if (!isset($options['callback']) || !is_callable($options['callback'])) {
                    throw new InvalidArgumentException('Custom cast requires a callable callback.');
                }
                return call_user_func($options['callback'], $value, $key, $this->data);
            default:
                throw new InvalidArgumentException("Unknown cast type: {$type}");
        }
    }

    protected function castInt($value): int
    {
        if (is_numeric($value)) {
            return (int)$value;
        }
        throw new InvalidArgumentException("Value '{$value}' is not an integer.");
    }

    protected function castFloat($value): float
    {
        if (is_numeric($value)) {
            return (float)$value;
        }
        throw new InvalidArgumentException("Value '{$value}' is not a float.");
    }

    protected function castBool($value): bool
    {
        if (is_bool($value)) return $value;
        if (in_array($value, ['1', 1, 'true', 'on', 'yes'], true)) return true;
        if (in_array($value, ['0', 0, 'false', 'off', 'no'], true)) return false;
        throw new InvalidArgumentException("Value '{$value}' is not a boolean.");
    }

    protected function castString($value): string
    {
        if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
            return (string)$value;
        }
        throw new InvalidArgumentException("Value is not a string.");
    }

    protected function castArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        throw new InvalidArgumentException("Value is not an array.");
    }

    /**
     * Validate and return a URL string.
     */
    protected function castUrl($value): string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return (string)$value;
        }
        throw new InvalidArgumentException("Value '{$value}' is not a valid URL.");
    }

    /**
     * Cast to a backed Enum or UnitEnum.
     * @param mixed $value
     * @param string $enumClass
     * @return UnitEnum
     */
    protected function castEnum($value, string $enumClass): UnitEnum
    {
        if (!enum_exists($enumClass)) {
            throw new InvalidArgumentException("Enum class '{$enumClass}' does not exist.");
        }

        // For backed enums (string/int)
        if (method_exists($enumClass, 'from')) {
            try {
                return $enumClass::from($value);
            } catch (\ValueError $e) {
                throw new InvalidArgumentException("Value '{$value}' is not valid for enum {$enumClass}.");
            }
        }

        // For pure/unit enums (no backing values)
        foreach ($enumClass::cases() as $case) {
            if ($case->name === $value) {
                return $case;
            }
        }

        throw new InvalidArgumentException("Value '{$value}' is not valid for enum {$enumClass}.");
    }
}