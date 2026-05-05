<?php

namespace Druidvav\EssentialsBundle;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\Attribute\Required;

trait DoctrineAwareTrait
{
    /**
     * @Required
     */
    #[Required]
    public ManagerRegistry $doctrine;

    protected function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }

    protected function getConnection(): Connection
    {
        return $this->getEm()->getConnection();
    }

    protected function getEm(): EntityManagerInterface
    {
        $manager = $this->doctrine->getManager();
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('The manager is not an instance of EntityManagerInterface.');
        }
        return $manager;
    }
}