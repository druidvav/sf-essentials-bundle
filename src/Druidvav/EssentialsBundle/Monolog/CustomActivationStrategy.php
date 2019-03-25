<?php
namespace Druidvav\EssentialsBundle\Monolog;

use Druidvav\EssentialsBundle\ContainerAwareTrait;
use Monolog\Handler\FingersCrossed\ActivationStrategyInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Monolog\Logger;

class CustomActivationStrategy implements ActivationStrategyInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function isHandlerActivated(array $record) {
        if(isset($record['context']) && isset($record['context']['exception']) && ($record['context']['exception'] instanceof HttpException)) {
            return false;
        }
        return $record['level'] >= Logger::NOTICE;
    }
}