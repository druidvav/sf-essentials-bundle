<?php
namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

/**
 * Trait RequestAwareTrait
 * @deprecated
 * @package Druidvav\EssentialsBundle\Service\ContainerService
 */
trait RequestAwareTrait
{
    use ContainerAwareTrait;

    /**
     * Shortcut to return the request service.
     *
     * @deprecated
     * @return Request
     */
    public function getRequest()
    {
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
