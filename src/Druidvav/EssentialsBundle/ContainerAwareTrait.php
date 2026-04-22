<?php

namespace Druidvav\EssentialsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait
{
    protected ?ContainerInterface $container = null;

    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
