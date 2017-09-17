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
 * Class Container
 * @package Katora
 */
class Container implements ContainerInterface, \ArrayAccess
{
    /**
     * @var array
     */
    protected $entries;

    /**
     * Container constructor.
     * @param array $entries
     */
    public function __construct(array $entries = array())
    {
        $this->entries = $entries;
    }

    // <editor-fold desc="Property access">

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
    }

    // </editor-fold>

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $entry = $this->entries[$id];
            if (is_callable($entry)) {
                $entry = call_user_func($entry, $this);
            }
            return $entry;
        }
        throw new NotFoundException("Service with ID '$id' not found.");
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (is_string($id)) {
            return isset($this->entries[$id]);
        }
        throw new ContainerException(sprintf(
            'Service ID must be a string, %s given.',
            is_scalar($id) ? gettype($id) : get_class($id)
        ));
    }

    /**
     * @param ServiceProviderInterface $provider
     * @return static
     */
    public function install(ServiceProviderInterface $provider)
    {
        $provider->provide($this);
        return $this;
    }

    // <editor-fold desc="Array access">

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
    }

    // </editor-fold>

    /**
     * @param callable $entry
     * @return callable
     */
    public function raw($entry)
    {
        return function () use ($entry) {
            return $entry;
        };
    }

    /**
     * @param string $id
     * @param mixed $entry
     * @return static
     */
    public function set($id, $entry)
    {
        if (is_string($id)) {
            $this->entries[$id] = $entry;
            return $this;
        }
        throw new ContainerException(sprintf(
            'Service ID must be a string, %s given.',
            is_scalar($id) ? gettype($id) : get_class($id)
        ));
    }

    /**
     * @param callable $entry
     * @return callable
     */
    public function share($entry)
    {
        return function ($container) use ($entry) {
            static $called = false, $resolved;
            if (true !== $called) {
                $resolved = call_user_func($entry, $container);
                $called = true;
            }
            return $resolved;
        };
    }
}
