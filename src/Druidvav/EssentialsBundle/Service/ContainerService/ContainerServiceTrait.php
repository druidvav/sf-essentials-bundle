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

    public function getDoctrine(): Registry
    {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->container->get('doctrine');
    }

    protected function getEm(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
}
