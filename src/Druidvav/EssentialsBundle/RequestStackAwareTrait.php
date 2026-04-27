<?php

namespace Druidvav\EssentialsBundle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Service\Attribute\Required;

trait RequestStackAwareTrait
{
    /**
     * @Required
     */
    public RequestStack $requestStack;

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }
}