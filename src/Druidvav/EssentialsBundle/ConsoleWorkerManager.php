<?php
namespace Druidvav\EssentialsBundle;

use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Process\Process;

class ConsoleWorkerManager
{
    use LoggerAwareTrait;

    protected $env;
    protected $console;

    protected $workers;
    protected $processes;
    protected $isShuttingDown = false;

    public function setEnv($env)
    {
        $this->env = $env;
    }

    public function setConsole($console)
    {
        $this->console = $console;
    }

    public function addWorker($command, $count)
    {
        $this->workers[] = [
            'command' => $command,
            'count' => $count,
        ];
    }

    public function killHanged()
    {
        $this->logger->info("Killing old possible hanged processes...");
        foreach ($this->workers as $worker) {
            system('pkill -9 -f ' . escapeshellarg($worker['command']));
        }
        $this->logger->info("Killing finished!");
    }

    public function start()
    {
        $this->logger->info('Started with pid=' . getmypid());

        pcntl_signal(SIGINT, function () {
            $this->logger->info('Got SIGINT, stopping workers...');
            $this->shutdown();
        });

        pcntl_signal(SIGTERM, function () {
            $this->logger->info('Got SIGTERM, stopping workers...');
            $this->shutdown();
        });

        $this->logger->info("Starting worker processes...");
        $this->processes = [ ];
        foreach ($this->workers as $worker) {
            for ($wi = 1; $wi <= $worker['count']; $wi++) {
                $pi = sizeof($this->processes);
                $this->processes[$pi] = [
                    'title' => "Worker #{$wi} for {$worker['command']}",
                    'command' => 'exec ' . $this->console . ' ' . $worker['command'] . ' -e ' . $this->env,
                    'process' => null,
                    'retryCount' => 0
                ];
                $this->startProcess($pi);
                sleep(1);
            }
        }
        $this->logger->info("Starting finished!");

        $this->running();
    }

    protected function running()
    {
        do {
            foreach ($this->processes as $pi => &$worker) {
                /* @var $process Process */
                $process = &$worker['process'];
                if (!$process->isRunning()) {
                    $this->logger->info("Looks like '{$worker['title']}' process is down...");
                    if ($worker['retryCount'] > 5) {
                        $this->logger->critical('Retry count exceeded for worker ' . $pi, [
                            'output' => $process->getOutput()
                        ]);
                        $this->shutdown();
                    } else {
                        $this->startProcess($pi);
                        $worker['retryCount']++;
                    }
                } else {
                    $worker['retryCount'] = 0;
                }
            }
            pcntl_signal_dispatch();
            sleep(1);
        } while (!$this->isShuttingDown);
    }

    protected function shutdown()
    {
        $this->isShuttingDown = true;
        foreach ($this->processes as $i => &$worker) {
            /* @var $process Process */
            $process = &$worker['process'];
            if ($process->isRunning()) {
                $this->logger->info("Stopping #$i");
                $process->stop();
            } else {
                $this->logger->info("Already stopped #$i");
            }
        }
        exit;
    }

    protected function startProcess($pi)
    {
        $process = &$this->processes[$pi]['process'];
        $process = new Process($this->processes[$pi]['command']);
        $process->start();
        $pid = $process->getPid();
        $this->logger->info("{$this->processes[$pi]['title']} started, pid={$pid}");
    }
}