<?php
/** @noinspection PhpUnusedParameterInspection */
namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Druidvav\EssentialsBundle\ContainerAwareTrait;
use LogicException;

/**
 * @internal
 */
trait ContainerServiceTrait
{
    use ContainerAwareTrait;

    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return Registry|object
     * @throws LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }
        return $this->container->get('doctrine');
    }

    /**
     * @return ObjectManager|EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}
