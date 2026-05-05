<?php

namespace Druidvav\EssentialsBundle;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LoggerAwareTrait
{
    protected LoggerInterface $logger;

    /**
     * @Required
     */
    #[Required]
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
