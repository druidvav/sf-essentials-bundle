<?php
/**
 * We'll assume that is internal library :)
 */
/** @noinspection PhpUnusedParameterInspection */
/** @noinspection PhpStatementHasEmptyBodyInspection */
namespace Druidvav\EssentialsBundle;

use Druidvav\EssentialsBundle\Service\ContainerService;
use Druidvav\EssentialsBundle\Service\ContainerService\ContainerServiceTrait;
use Exception;
use LogicException;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\Process;

abstract class Command extends BaseCommand implements ContainerInterface
{
    use ContainerServiceTrait;
    use LoggerAwareTrait;

    public function get($id, $invalidBehavior = ContainerService::EXCEPTION_ON_INVALID_REFERENCE)
    {
        return $this->container->get($id, $invalidBehavior);
    }

    public function set($id, $service)
    {
        throw new LogicException('Not available here');
    }

    public function has($id)
    {
        return $this->container->has($id);
    }

    public function initialized($id)
    {
        return $this->container->initialized($id);
    }

    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    public function hasParameter($name)
    {
        return $this->container->hasParameter($name);
    }

    public function setParameter($name, $value)
    {
        throw new LogicException('Not available here');
    }

    protected function checkRunning($command)
    {
        $process = new Process('ps auxww | grep "console ' . $command . ' " | grep -v grep | grep -v "\/bin\/sh" | wc -l');
        $process->start();
        while ($process->isRunning()) {
            // waiting for process to finish
        }
        if (intval($process->getOutput()) > 1) {
            $this->getLogger()->info($command . ' is already running');
            exit;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param $task
     * @throws Exception
     */
    protected function executeTask(InputInterface $input, OutputInterface $output, $task)
    {
        $log = $this->getLogger();
        $log->info('Running ' . $task . '...');
        $command = $this->getApplication()->find($task);
        $command->run(new ArrayInput([ ]), $output);
        $log->info('Finished ' . $task . '!');
    }
}