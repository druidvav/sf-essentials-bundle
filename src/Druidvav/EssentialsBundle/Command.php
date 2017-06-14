<?php
namespace Druidvav\EssentialsBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class Command extends BaseCommand
{
    protected function checkRunning($command)
    {
        $process = new Process('ps aux | grep "console ' . $command . ' " | grep -v grep | grep -v "\/bin\/sh" | wc -l');
        $process->start();
        while ($process->isRunning()) {
            // waiting for process to finish
        }
        if (intval($process->getOutput()) > 1) {
            $this->getContainer()->get('monolog.logger.console')->critical($command . ' is already running');
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

    /**
     * @return Registry|object
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->getContainer()->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return ObjectManager|EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return Logger|object
     */
    protected function getLogger()
    {
        if (!$this->getContainer()->has('logger')) {
            throw new \LogicException('The MonologBundle is not registered in your application.');
        }
        return $this->getContainer()->get('logger');
    }
}