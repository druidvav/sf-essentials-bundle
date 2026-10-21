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
        $process = new Process(['ps', '-axo', 'args=']);
        $process->run();

        if ($this->countRunningConsoleProcesses($process->getOutput(), $command) > 1) {
            $this->logger->info($command.' is already running');
            exit;
        }
    }

    private function countRunningConsoleProcesses(string $processList, string $command): int
    {
        $count = 0;
        $commandPattern = '~(?:^|\s)(?:\S*/)?console\s+'.preg_quote($command, '~').'(?=\s|$)~';

        foreach (preg_split('/\R/', $processList) as $line) {
            if (!preg_match('/^\s*(\S+)(?:\s|$)/', $line, $matches)) {
                continue;
            }

            $executable = basename($matches[1]);
            if (!preg_match('/^php(?:@?\d+(?:\.\d+)*)?$/i', $executable)) {
                continue;
            }

            if (preg_match($commandPattern, $line)) {
                ++$count;
            }
        }

        return $count;
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
