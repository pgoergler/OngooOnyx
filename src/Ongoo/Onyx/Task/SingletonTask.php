<?php

namespace Ongoo\Onyx\Task;

/**
 * Description of SingletonTask
 *
 * @author paul
 */
abstract class SingletonTask extends Task
{

    use \Symfony\Component\Console\Command\LockableTrait;
    
    protected $lockFile = null;

    protected function configure()
    {
        parent::configure();
        $this->addOption('pid-file', null, \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'PID file to exclude concurent run', null);
        $this->addOption('logger', null, \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED, 'Logger name to use', 'cli');
    }

    protected function onStart(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        parent::onStart($input, $output);

        $loggerName = $input->getOption('logger');

        $root = $this->app['logger.factory']->get($loggerName);
        $root->set('app', $this->getStrName());
        $this->app['logger.factory']->add($root, 'root');
        $this->app['logger'] = $root;

        if (!$this->lock())
        {
            $output->writeln('<error>The command is already running in another process.</error>');
            return 0;
        }
    }

    protected function onFinish()
    {
        parent::onFinish();
        $this->release();
    }

}
