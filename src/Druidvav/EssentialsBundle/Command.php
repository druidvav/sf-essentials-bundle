<?php
/**
 * @noinspection PhpInternalEntityUsedInspection
 * We'll assume that is internal library :)
 */
namespace Druidvav\EssentialsBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Druidvav\EssentialsBundle\Service\ContainerService\ContainerServiceTrait;
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

    protected function executeTask(InputInterface $input, OutputInterface $output, $task)
    {
        $log = $this->getLogger();
        $log->info('Running ' . $task . '...');
        $command = $this->getApplication()->find($task);
        $command->run(new ArrayInput([ ]), $output);
        $log->info('Finished ' . $task . '!');
    }
}