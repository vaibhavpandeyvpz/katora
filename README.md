# Katora

[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/katora.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/katora)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/katora.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/katora)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/katora.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/katora)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/katora.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vaibhavpandeyvpz/katora/tests.yml?branch=master&style=flat-square)](https://github.com/vaibhavpandeyvpz/katora/actions)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg?style=flat-square)](https://github.com/vaibhavpandeyvpz/katora)

Simple, lightweight service container implementing [PSR-11](https://www.php-fig.org/psr/psr-11/) for PHP 8.2+.

> **Katora** (`कटोरा`) means "Bowl" in Hindi - a container that holds things together.

## Features

- ✅ **PSR-11 Compliant** - Full implementation of the ContainerInterface standard
- ✅ **Lazy Resolution** - Services defined as callables are resolved only when accessed
- ✅ **Singleton Support** - Built-in `share()` method for singleton pattern
- ✅ **Service Providers** - Modular service registration via providers
- ✅ **Multiple Access Patterns** - Use methods, array access, or property access
- ✅ **Type Safe** - Full PHP 8.2+ type hints and return types
- ✅ **100% Test Coverage** - Comprehensive test suite with full coverage
- ✅ **Zero Dependencies** - Only requires PSR-11 interface (no other dependencies)

## Installation

Install via Composer:

```bash
composer require vaibhavpandeyvpz/katora
```

## Quick Start

```php
use Katora\Container;

// Create a container
$container = new Container();

// Register a simple value
$container->set('app.name', 'My Application');

// Register a service with lazy resolution
$container->set('database', fn() => new DatabaseConnection());

// Retrieve services
$appName = $container->get('app.name');
$db = $container->get('database');
```

## Usage

### Basic Operations

#### Setting Services

```php
use Katora\Container;

$container = new Container();

// Simple values
$container->set('config.debug', true);
$container->set('config.timezone', 'UTC');

// Objects
$container->set('logger', new Logger());

// Lazy resolution with closures
$container->set('cache', fn() => new RedisCache());
```

#### Getting Services

```php
// Direct method call
$logger = $container->get('logger');

// Check if service exists
if ($container->has('logger')) {
    $logger = $container->get('logger');
}
```

#### Multiple Access Patterns

```php
// Method access
$container->set('service', 'value');
$value = $container->get('service');

// Array access
$container['service'] = 'value';
$value = $container['service'];

// Property access
$container->service = 'value';
$value = $container->service;
```

### Lazy Service Resolution

Services defined as callables are automatically resolved when accessed:

```php
$container->set('user.repository', function (ContainerInterface $c) {
    return new UserRepository($c->get('database'));
});

// The closure is only executed when get() is called
$userRepo = $container->get('user.repository');
```

### Singleton Pattern

Use `share()` to ensure a service is resolved only once:

```php
$container->set('singleton.service', $container->share(function () {
    return new ExpensiveService();
}));

// First call resolves and caches
$service1 = $container->get('singleton.service');

// Subsequent calls return the cached instance
$service2 = $container->get('singleton.service');
// $service1 === $service2
```

### Raw Callables

Prevent automatic resolution when you need to store a callable as a value:

```php
$container->set('callback', $container->raw(function () {
    return 'This is a callable, not a service';
}));

// Returns the callable itself, not its result
$callback = $container->get('callback');
$result = $callback(); // Call it manually
```

### Service Providers

Organize service registration with providers:

```php
use Katora\Container;
use Katora\ServiceProviderInterface;

class DatabaseServiceProvider implements ServiceProviderInterface
{
    public function provide(Container $container): void
    {
        $container->set('database.config', [
            'host' => 'localhost',
            'port' => 3306,
        ]);

        $container->set('database', $container->share(function (ContainerInterface $c) {
            $config = $c->get('database.config');
            return new Database($config);
        }));
    }
}

// Install the provider
$container->install(new DatabaseServiceProvider());
```

### Constructor Initialization

You can pre-populate the container:

```php
$container = new Container([
    'app.name' => 'My App',
    'app.version' => '1.0.0',
    'database' => fn() => new Database(),
]);
```

### Method Chaining

Most methods return `$this` for fluent interfaces:

```php
$container
    ->set('service1', 'value1')
    ->set('service2', 'value2')
    ->install(new ServiceProvider())
    ->set('service3', 'value3');
```

### Container-Aware Classes

Use the `HasContainer` trait to make classes container-aware:

```php
use Katora\Extra\HasContainer;
use Katora\Extra\KeepsContainer;
use Psr\Container\ContainerInterface;

class MyService implements KeepsContainer
{
    use HasContainer;

    public function doSomething(): void
    {
        $logger = $this->getContainer()->get('logger');
        $logger->info('Doing something');
    }
}

$service = new MyService();
$service->setContainer($container);
```

## API Reference

### Container

#### `__construct(array $entries = [])`

Creates a new container instance, optionally with initial entries.

#### `get(string $id): mixed`

Retrieves a service from the container. Throws `NotFoundException` if the service doesn't exist.

**Throws:** `NotFoundException`

#### `has(string $id): bool`

Checks if a service exists in the container.

#### `set(string $id, mixed $entry): static`

Registers a service in the container. Returns `$this` for method chaining.

#### `install(ServiceProviderInterface $provider): static`

Installs a service provider. Returns `$this` for method chaining.

#### `share(callable $entry): callable`

Wraps a callable to ensure it's only resolved once (singleton pattern).

#### `raw(callable $entry): callable`

Wraps a callable to prevent automatic resolution when retrieved.

### Exceptions

#### `ContainerException`

Base exception for container-related errors. Extends `InvalidArgumentException` and implements `ContainerExceptionInterface`.

#### `NotFoundException`

Thrown when a requested service is not found. Extends `ContainerException` and implements `NotFoundExceptionInterface`.

### Interfaces

#### `ServiceProviderInterface`

Interface for service providers. Implement this to create modular service registration.

**Methods:**

- `provide(Container $container): void` - Registers services with the container

#### `KeepsContainer`

Interface for container-aware classes. Use with the `HasContainer` trait.

**Methods:**

- `getContainer(): ?ContainerInterface` - Gets the container instance
- `setContainer(ContainerInterface $container): void` - Sets the container instance

## Requirements

- PHP >= 8.2
- PSR-11 Container Interface

## Testing

Run the test suite with PHPUnit:

```bash
vendor/bin/phpunit
```

With coverage:

```bash
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

**Vaibhav Pandey**

- GitHub: [@vaibhavpandeyvpz](https://github.com/vaibhavpandeyvpz)
- Email: contact@vaibhavpandey.com

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/vaibhavpandeyvpz/katora).
