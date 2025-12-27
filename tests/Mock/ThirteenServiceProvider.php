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
 */
class ThirteenServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function provide(Container $container): void
    {
        $container->set('thirteen', fn () => 13);
    }
}
