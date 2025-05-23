# Laravel OOP Config

[![MIT License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

**Laravel OOP Config** is a package that enables developers to generate type-safe, object-oriented wrappers for their Laravel configuration files. This approach provides code completion, type safety, and improved maintainability when working with configuration values.

---

## Features

- 🔒 **Type-safe config access:** Generate PHP classes and methods for config files.
- ⚡ **Batch config class generation:** Generate classes for all config files at once.
- 🛠 **Customizable stubs:** Publish and modify the code templates for generated classes and methods.
- 🧩 **Automatic service provider integration:** Seamlessly integrates with Laravel’s service container.
- 📝 **IDE autocompletion:** Enables modern IDEs to provide autocompletion for config keys.
- 🧪 **Tested & reliable:** Includes feature and unit tests for stable operation.

---

## Installation

Install via Composer:

```bash
composer require veiliglanceren/laravel-oop-config --dev
```

Publish the configuration file (optional):

```bash
php artisan vendor:publish --tag=oop-config
```

---

## Configuration

After publishing, you can customize the config in `config/oop-config.php`:

- **namespace**: Namespace for generated config classes (default: `App\Config`).
- **path**: Directory path where generated config classes are stored (default: `app/Config`).
- **autoload**: Whether to automatically register generated classes (default: `true`).
- **stubs**: Paths to custom stubs for class and method generation.

---

## Usage

### Generate a Config Class

To generate a class for a specific config file (e.g., `config/mail.php`):

```bash
php artisan make:config mail
```

This creates `App\Config\MailConfig`, with methods corresponding to the keys in `mail.php`.

### Generate Classes for All Config Files

```bash
php artisan config:generate-all
```

Add `--force` to overwrite existing classes:

```bash
php artisan config:generate-all --force
```

### Accessing Config Values

Use the generated class to access config values with autocompletion and type-safety:

```php
use App\Config\MailConfig;

$mailConfig = app(MailConfig::class);

$host = $mailConfig->host();
$fromAddress = $mailConfig->fromAddress();
```

For nested config arrays, methods are generated using camelCase:

```php
$fromAddress = $mailConfig->fromAddress();
```

---

## Customizing Code Generation

### Custom Stubs

You can customize the generated code by publishing the default stubs:

```bash
php artisan vendor:publish --tag=stubs
```

Edit the stub files in the published `stubs` directory, then update the `stubs` paths in `config/oop-config.php` if needed.

---

## Testing

This package uses [Pest](https://pestphp.com/). To run the tests:

```bash
./vendor/bin/pest
```

Ensure your test environment is configured according to Laravel’s testing guidelines.

---

## Troubleshooting & FAQ

- **Config is not an array:** Ensure that your config files return arrays.
- **Missing or unreadable stub file:** Check the `stubs` path in your config and ensure files exist and are readable.
- **Classes not autoloaded:** Make sure `autoload` is enabled in `oop-config.php`, and clear the config cache if needed.

---

## Contributing

Contributions are welcome! Please:

1. Fork the repository.
2. Create a feature branch.
3. Write tests for your changes.
4. Submit a pull request.

For suggestions, bug reports, or feature requests, [open an issue](https://github.com/VeiligLanceren-nl/laravel-oop-config/issues).

---

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

---

**Made with ❤️ by [VeiligLanceren.nl](https://veiliglanceren.nl), credits for the idea to [Oussama Mater](https://www.linkedin.com/in/oussamamater) on [LinkedIn](https://www.linkedin.com/feed/update/urn:li:share:7327066187912101889/)**