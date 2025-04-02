<?php

namespace FrameJam\Console\Commands;

use FrameJam\Core\Queue\Worker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class QueueWorkCommand extends Command
{
    protected static $defaultName = 'queue:work';

    protected function configure(): void
    {
        $this->setDescription('Start the queue worker')
             ->addOption('queue', null, InputOption::VALUE_OPTIONAL, 'The queue to listen on', 'default')
             ->addOption('memory', null, InputOption::VALUE_OPTIONAL, 'The memory limit in megabytes', 128)
             ->addOption('timeout', null, InputOption::VALUE_OPTIONAL, 'The number of seconds a job can run', 60)
             ->addOption('sleep', null, InputOption::VALUE_OPTIONAL, 'Number of seconds to sleep when no job is available', 3)
             ->addOption('tries', null, InputOption::VALUE_OPTIONAL, 'Number of times to attempt a job before logging it failed', 3)
             ->addOption('max-jobs', null, InputOption::VALUE_OPTIONAL, 'Maximum number of jobs to process before stopping', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $worker = new Worker([
            'queue' => $input->getOption('queue'),
            'memory' => (int) $input->getOption('memory'),
            'timeout' => (int) $input->getOption('timeout'),
            'sleep' => (int) $input->getOption('sleep'),
            'tries' => (int) $input->getOption('tries'),
            'max_jobs' => $input->getOption('max-jobs') ? (int) $input->getOption('max-jobs') : null,
        ]);

        $output->writeln('<info>Starting queue worker...</info>');
        
        try {
            $worker->start();
        } catch (\Throwable $e) {
            $output->writeln('<error>Worker failed: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
} 