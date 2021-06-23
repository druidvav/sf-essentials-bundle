<?php
namespace Druidvav\EssentialsBundle\Service\ContainerService;

use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

trait RouterAwareTrait
{
    use ContainerAwareTrait;

    public function generateUrl(string $route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }
}
