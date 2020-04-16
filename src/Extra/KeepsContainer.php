<?php

namespace Katora\Extra;

use Psr\Container\ContainerInterface;

interface KeepsContainer
{
    public function getContainer();

    public function setContainer(ContainerInterface $container);
}
