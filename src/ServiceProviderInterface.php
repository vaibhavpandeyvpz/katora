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
 * Interface for service providers.
 *
 * Service providers allow for modular registration of services in the container.
 * Implement this interface to create a provider that can register multiple
 * related services at once.
 */
interface ServiceProviderInterface
{
    /**
     * Registers services with the container.
     *
     * This method is called when the provider is installed via Container::install().
     * Use this method to register all services provided by this provider.
     *
     * @param  Container  $container  The container instance to register services with
     */
    public function provide(Container $container): void;
}
