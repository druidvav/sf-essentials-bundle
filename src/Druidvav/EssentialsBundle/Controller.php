<?php

namespace Druidvav\EssentialsBundle;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseController;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @deprecated Use your own base controller and inject the services you need instead.
 *             This class will be removed in final version 3.0 of this bundle.
 */
abstract class Controller extends BaseController
{
    use LoggerAwareTrait;
    use DoctrineAwareTrait;

    protected function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    protected function getFlash($type)
    {
        if (!$this->has('session')) {
            throw new LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        return $this->get('session')->getFlashBag()->get($type);
    }
}
