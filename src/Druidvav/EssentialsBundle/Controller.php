<?php
namespace Druidvav\EssentialsBundle;

use Doctrine\ORM\EntityManager;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;

abstract class Controller extends BaseController
{
    use LoggerAwareTrait;

    protected function getContainer()
    {
        return $this->container;
    }

    protected function getFlash($type)
    {
        if (!$this->container->has('session')) {
            throw new LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        return $this->container->get('session')->getFlashBag()->get($type);
    }

    /**
     * @return EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}