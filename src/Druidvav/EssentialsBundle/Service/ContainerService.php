<?php
namespace Druidvav\EssentialsBundle\Service;

use Druidvav\EssentialsBundle\Service\ContainerService\ContainerServiceTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerService implements ContainerInterface
{
    use ContainerServiceTrait;

    public function __construct(ContainerInterface $container = null)
    {
        if ($container !== null) {
            $this->container = $container;
        }
    }
}