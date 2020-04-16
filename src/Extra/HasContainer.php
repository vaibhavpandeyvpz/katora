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
 * Trait HasContainer
 * @package Katora\Extra
 */
trait HasContainer
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
