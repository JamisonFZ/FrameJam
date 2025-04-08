<?php

namespace FrameJam\Commands;

use FrameJam\Core\Database\MigrationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RollbackCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('migrate:rollback')
            ->setDescription('Reverte a última execução de migrações');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Revertendo migrações...');

        try {
            $migrationManager = new MigrationManager(__DIR__ . '/../Database/Migrations');
            $migrationManager->rollback();
            
            $io->success('Todas as migrações foram revertidas com sucesso!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erro ao reverter migrações: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 