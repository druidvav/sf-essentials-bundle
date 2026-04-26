<?php

namespace Druidvav\EssentialsBundle\Service;

use Druidvav\EssentialsBundle\DoctrineAwareTrait;
use Druidvav\EssentialsBundle\Service\ContainerService\ContainerServiceTrait;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ContainerService implements ContainerInterface
{
    use ContainerServiceTrait;
    use DoctrineAwareTrait;

    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE): ?object
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function set($id, $service)
    {
        throw new LogicException('Not available here');
    }

    public function setParameter($name, $value): void
    {
        throw new LogicException('Not available here');
    }

    public function has($id): bool
    {
        return $this->container->has($id);
    }

    public function initialized($id): bool
    {
        return $this->container->initialized($id);
    }

    /**
     * PHP 7.4 совместимость: union-тип появится в интерфейсе только на PHP 8+.
     *
     * @return array|bool|string|int|float|\UnitEnum|null
     */
    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    public function hasParameter($name): bool
    {
        return $this->container->hasParameter($name);
    }
}
