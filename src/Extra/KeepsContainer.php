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
 * Interface for classes that can store and retrieve a container instance.
 *
 * This interface is useful for classes that need container awareness,
 * allowing them to access services from a dependency injection container.
 * The HasContainer trait provides a default implementation of this interface.
 *
 * @see HasContainer
 */
interface KeepsContainer
{
    /**
     * Retrieves the container instance.
     *
     * @return \Psr\Container\ContainerInterface|null The container instance, or null if not set
     */
    public function getContainer(): ?ContainerInterface;

    /**
     * Sets the container instance.
     *
     * @param  \Psr\Container\ContainerInterface  $container  The container instance to set
     */
    public function setContainer(ContainerInterface $container): void;
}
