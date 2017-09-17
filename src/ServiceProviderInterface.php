<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora;

/**
 * Interface ServiceProviderInterface
 * @package Katora
 */
interface ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function provide(Container $container);
}
