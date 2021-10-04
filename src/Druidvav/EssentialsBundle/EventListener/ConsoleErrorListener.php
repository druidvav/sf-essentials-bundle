<?php
namespace Druidvav\EssentialsBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleEvent;

class ConsoleErrorListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $exception = $event->getError();

        $this->logger->error('Error thrown while running command "{command}". Message: "{message}"', [
            'exception' => $exception,
            'message' => $exception->getMessage(),
            'command' => $this->getInputString($event)
        ]);
        $event->stopPropagation();
    }

    private static function getInputString(ConsoleEvent $event): array|string|null
    {
        $commandName = $event->getCommand()?->getName();
        $input = $event->getInput();

        if (method_exists($input, '__toString')) {
            if ($commandName) {
                return str_replace(array("'$commandName'", "\"$commandName\""), $commandName, (string) $input);
            }

            return (string) $input;
        }

        return $commandName;
    }
}