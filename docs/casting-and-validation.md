# Config Value Casting and Validation

## Overview

With the new `AbstractConfig` base class, you can now add type casting and value validation to your generated config classes. This makes your configuration safer and more robust, ensuring you always receive the expected data types.

## Supported Casts

- **int**: Casts the value to an integer, or throws if not valid.
- **float**: Casts the value to a float, or throws if not valid.
- **bool**: Casts common boolean representations.
- **string**: Ensures the value is a string.
- **array**: Ensures the value is an array.
- **url**: Validates the value is a valid URL string.
- **enum:Your\Enum\Class**: Casts to an enum case, throws if invalid.
- **custom**: Pass a custom callable for bespoke validation/casting.

## Usage Example

```php
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

    public function customValue()
    {
        return $this->get('custom', 'custom', [
            'callback' => function($value) {
                if ($value !== 'foo') throw new \InvalidArgumentException('Custom value must be "foo"');
                return $value;
            }
        ]);
    }
}