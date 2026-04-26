<?php

namespace Druidvav\EssentialsBundle;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\Service\Attribute\Required;

trait DoctrineAwareTrait
{
    /**
     * @Required
     */
    public ManagerRegistry $doctrine;

    protected function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    protected function getEm(): ObjectManager
    {
        return $this->doctrine->getManager();
    }
}