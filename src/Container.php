<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora;

use Psr\Container\ContainerInterface;

/**
 * Service container implementing PSR-11 ContainerInterface.
 *
 * This container provides dependency injection capabilities with support for:
 * - Lazy service resolution via callables
 * - Singleton pattern via share() method
 * - Service providers for modular service registration
 * - Multiple access patterns: direct methods, array access, and property access
 *
 * @implements \Psr\Container\ContainerInterface
 * @implements \ArrayAccess<string, mixed>
 */
#[\AllowDynamicProperties]
class Container implements \ArrayAccess, ContainerInterface
{
    /**
     * Internal storage for container entries.
     *
     * @var array<string, mixed> Map of service IDs to their definitions or resolved values
     */
    protected array $entries = [];

    /**
     * Container constructor.
     *
     * @param  array<string, mixed>  $entries  Initial entries to populate the container with
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    // <editor-fold desc="Property access">

    /**
     * Magic method to retrieve a service via property access.
     *
     * @param  string  $name  Service ID
     * @return mixed The resolved service value
     *
     * @throws NotFoundException If the service is not found
     */
    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    /**
     * Magic method to check if a service exists via property access.
     *
     * @param  string  $name  Service ID
     * @return bool True if the service exists, false otherwise
     */
    public function __isset(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Magic method to set a service via property access.
     *
     * @param  string  $name  Service ID
     * @param  mixed  $value  Service definition or value
     */
    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    /**
     * Magic method for unset operation.
     *
     * Note: Unset is intentionally not supported to maintain container integrity.
     *
     * @param  string  $name  Service ID
     */
    public function __unset(string $name): void
    {
        // Intentionally empty - unset is not supported
    }

    // </editor-fold>

    /**
     * {@inheritdoc}
     *
     * Retrieves a service from the container by its identifier.
     * If the entry is a callable, it will be invoked and the result cached.
     *
     * @param  string  $id  Service identifier
     * @return mixed The resolved service value
     *
     * @throws NotFoundException If the service is not found
     */
    public function get(string $id): mixed
    {
        if (! array_key_exists($id, $this->entries)) {
            throw new NotFoundException("Service with ID '$id' not found.");
        }

        $entry = $this->entries[$id];

        return is_callable($entry) ? $entry($this) : $entry;
    }

    /**
     * {@inheritdoc}
     *
     * Checks if a service exists in the container.
     *
     * @param  string  $id  Service identifier
     * @return bool True if the service exists, false otherwise
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * Installs a service provider to register services in the container.
     *
     * @param  ServiceProviderInterface  $provider  The service provider to install
     * @return $this Returns self for method chaining
     */
    public function install(ServiceProviderInterface $provider): static
    {
        $provider->provide($this);

        return $this;
    }

    // <editor-fold desc="Array access">

    /**
     * {@inheritdoc}
     *
     * Checks if an offset exists in the container (array access).
     *
     * @param  mixed  $offset  Service identifier
     * @return bool True if offset exists and is a string, false otherwise
     */
    public function offsetExists(mixed $offset): bool
    {
        return is_string($offset) && $this->has($offset);
    }

    /**
     * {@inheritdoc}
     *
     * Retrieves a service via array access syntax.
     *
     * @param  mixed  $offset  Service identifier
     * @return mixed The resolved service value
     *
     * @throws ContainerException If offset is not a string
     * @throws NotFoundException If the service is not found
     */
    public function offsetGet(mixed $offset): mixed
    {
        if (! is_string($offset)) {
            throw new ContainerException(sprintf(
                'Service ID must be a string, %s given.',
                get_debug_type($offset)
            ));
        }

        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     *
     * Sets a service via array access syntax.
     *
     * @param  mixed  $offset  Service identifier
     * @param  mixed  $value  Service definition or value
     *
     * @throws ContainerException If offset is not a string
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (! is_string($offset)) {
            throw new ContainerException(sprintf(
                'Service ID must be a string, %s given.',
                get_debug_type($offset)
            ));
        }
        $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     *
     * Unset operation for array access.
     * Note: Unset is intentionally not supported to maintain container integrity.
     *
     * @param  mixed  $offset  Service identifier
     */
    public function offsetUnset(mixed $offset): void
    {
        // Intentionally empty - unset is not supported
    }

    // </editor-fold>

    /**
     * Wraps a callable to prevent automatic resolution when retrieved.
     *
     * When a callable is wrapped with raw(), it will be returned as-is when
     * retrieved from the container, rather than being automatically invoked.
     * This is useful when you need to store a callable as a value rather than
     * as a service factory.
     *
     * @param  callable  $entry  The callable to wrap
     * @return callable(): callable Returns a callable that when invoked returns the original callable
     */
    public function raw(callable $entry): callable
    {
        return fn () => $entry;
    }

    /**
     * Registers a service in the container.
     *
     * @param  string  $id  Service identifier
     * @param  mixed  $entry  Service definition (can be a value or a callable for lazy resolution)
     * @return $this Returns self for method chaining
     */
    public function set(string $id, mixed $entry): static
    {
        $this->entries[$id] = $entry;

        return $this;
    }

    /**
     * Wraps a callable to ensure it's only resolved once (singleton pattern).
     *
     * When a callable is wrapped with share(), it will be invoked only once
     * and the result will be cached. Subsequent retrievals will return the
     * same cached value, ensuring singleton behavior.
     *
     * Each closure instance created by share() maintains its own resolved value,
     * so different services wrapped with share() will have independent caches.
     *
     * @param  callable(ContainerInterface): mixed  $entry  The callable factory to wrap
     * @return callable(ContainerInterface): mixed Returns a callable that resolves to a singleton value
     */
    public function share(callable $entry): callable
    {
        return function (ContainerInterface $container) use ($entry) {
            static $resolved = null;
            static $initialized = false;

            if (! $initialized) {
                $resolved = $entry($container);
                $initialized = true;
            }

            return $resolved;
        };
    }
}
