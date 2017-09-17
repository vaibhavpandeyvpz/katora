<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora\Mock;

use Katora\Container;
use Katora\ServiceProviderInterface;

/**
 * Class ThirteenServiceProvider
 * @package Katora\Mock
 */
class ThirteenServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provide(Container $container)
    {
        $container->set('thirteen', function () {
            return 13;
        });
    }
}
