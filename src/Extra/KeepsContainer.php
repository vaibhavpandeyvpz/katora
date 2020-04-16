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
 * Interface KeepsContainer
 * @package Katora\Extra
 */
interface KeepsContainer
{
    /**
     * @return ContainerInterface|null
     */
    public function getContainer();

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);
}
