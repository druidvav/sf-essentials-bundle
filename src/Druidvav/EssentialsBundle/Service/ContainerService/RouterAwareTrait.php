<?php
namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait RouterAwareTrait
{
    use ContainerAwareTrait;

    /**
     * Generates a URL from the given parameters.
     *
     * @param string      $route         The name of the route
     * @param mixed       $parameters    An array of parameters
     * @param bool|string $referenceType The type of reference (one of the constants in UrlGeneratorInterface)
     *
     * @return string The generated URL
     *
     * @see UrlGeneratorInterface
     */
    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
