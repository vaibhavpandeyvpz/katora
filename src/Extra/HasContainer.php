<?php

namespace Katora\Extra;

use Psr\Container\ContainerInterface;

trait HasContainer
{
    /**
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * @return ContainerInterface|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
