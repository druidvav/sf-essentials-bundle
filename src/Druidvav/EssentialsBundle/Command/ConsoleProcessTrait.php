<?php

namespace Druidvav\EssentialsBundle\Command;

use Druidvav\EssentialsBundle\LoggerAwareTrait;
use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * @method Application|null getApplication()
 */
trait ConsoleProcessTrait
{
    use LoggerAwareTrait;

//    abstract private function getApplication(): ?Application;

    protected function checkRunning($command): void
    {
        $process = Process::fromShellCommandline('ps auxww | grep "console '.$command.' " | grep -v grep | grep -v "\/bin\/sh" | wc -l');
        $process->start();
        /** @noinspection PhpStatementHasEmptyBodyInspection */
        while ($process->isRunning()) {
            // waiting for process to finish
        }
        if ((int) $process->getOutput() > 1) {
            $this->logger->info($command.' is already running');
            exit;
        }
    }

    /**
     * @throws Exception
     */
    protected function executeTask(InputInterface $input, OutputInterface $output, $task)
    {
        $log = $this->getLogger();
        $log->info('Running '.$task.'...');
        $command = $this->getApplication()->find($task);
        $command->run(new ArrayInput([]), $output);
        $log->info('Finished '.$task.'!');
    }

}