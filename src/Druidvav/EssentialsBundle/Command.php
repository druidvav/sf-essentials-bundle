<?php
namespace Druidvav\EssentialsBundle;

use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand as BaseCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

abstract class Command extends BaseCommand
{
    use LoggerAwareTrait;

    /**
     * @param string $id The service id
     * @return bool true if the service id is defined, false otherwise
     * @final
     */
    protected function has($id)
    {
        return $this->getContainer()->has($id);
    }

    /**
     * @param string $id The service id
     * @return object The service
     * @final
     */
    protected function get($id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @param string $name The parameter name
     * @return mixed
     * @final
     */
    protected function getParameter($name)
    {
        return $this->getContainer()->getParameter($name);
    }

    /**
     * @return Registry|object
     * @throws LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application.');
        }
        return $this->get('doctrine');
    }

    /**
     * @return ObjectManager|EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
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

    protected function executeTask(InputInterface $input, OutputInterface $output, $task)
    {
        $log = $this->getLogger();
        $log->info('Running ' . $task . '...');
        $command = $this->getApplication()->find($task);
        $command->run(new ArrayInput([ ]), $output);
        $log->info('Finished ' . $task . '!');
    }
}