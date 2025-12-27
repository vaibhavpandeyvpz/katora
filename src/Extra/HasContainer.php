<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora\Extra;

use Psr\Container\ContainerInterface;

/**
 * Trait that provides container awareness to classes.
 *
 * This trait implements the KeepsContainer interface, allowing classes to
 * store and retrieve a container instance. Useful for classes that need
 * dependency injection capabilities.
 *
 * @see KeepsContainer
 */
trait HasContainer
{
    /**
     * The container instance.
     */
    protected ?ContainerInterface $container = null;

    /**
     * Retrieves the container instance.
     *
     * @return \Psr\Container\ContainerInterface|null The container instance, or null if not set
     */
    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }

    /**
     * Sets the container instance.
     *
     * @param  \Psr\Container\ContainerInterface  $container  The container instance to set
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
