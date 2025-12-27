<?php

/*
 * This file is part of vaibhavpandeyvpz/katora package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Katora;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when a service is not found in the container.
 *
 * This exception is thrown when attempting to retrieve a service that
 * has not been registered in the container.
 *
 * @implements \Psr\Container\NotFoundExceptionInterface
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface {}
