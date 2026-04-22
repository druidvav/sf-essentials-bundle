<?php

/** @noinspection PhpMissingFieldTypeInspection */

namespace Druidvav\EssentialsBundle;

use Psr\Container\ContainerInterface;

trait ContainerAwareTrait
{
    /**
     * Реально сюда внедряется Symfony-контейнер, но для autowiring используем PSR интерфейс
     * (иначе Symfony 5.1+ ругается на deprecated alias).
     *
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|null
     */
    protected $container;

    /**
     * @required
     */
    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
