<?php
/** @noinspection PhpUnusedParameterInspection */
namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Druidvav\EssentialsBundle\Service\ContainerService;
use LogicException;

trait ContainerServiceTrait
{
    use ContainerAwareTrait;

    public function getContainer()
    {
        return $this->container;
    }

    public function get($id, $invalidBehavior = ContainerService::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function set($id, $service)
    {
        throw new LogicException('Not available here');
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function initialized($id)
    {
        return $this->container->initialized($id);
    }

    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    public function hasParameter($name)
    {
        return $this->container->hasParameter($name);
    }

    public function setParameter($name, $value)
    {
        throw new LogicException('Not available here');
    }

    /**
     * @return Registry|object
     * @throws LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }
        return $this->get('doctrine');
    }

    /**
     * @return ObjectManager|EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}
