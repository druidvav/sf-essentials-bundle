<?php
/** @noinspection PhpMissingFieldTypeInspection */
namespace Druidvav\EssentialsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;

trait ContainerAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @required
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
