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
 * Class NotFoundException
 * @package Katora
 */
class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
}
