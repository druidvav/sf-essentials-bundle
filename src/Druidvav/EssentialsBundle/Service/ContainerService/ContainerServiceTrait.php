<?php

namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Persistence\ObjectManager;
use Druidvav\EssentialsBundle\ContainerAwareTrait;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @internal
 */
trait ContainerServiceTrait
{
    use ContainerAwareTrait;

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }
}
