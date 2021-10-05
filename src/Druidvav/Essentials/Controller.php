<?php
namespace Druidvav\Essentials;

use Doctrine\Persistence\ObjectManager;
use Druidvav\EssentialsBundle\LoggerAwareTrait;
use LogicException;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class Controller extends AbstractController
{
    use LoggerAwareTrait;

    protected function getContainer(): ContainerInterface
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

    protected function getEm(): ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }
}