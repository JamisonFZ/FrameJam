<?php

namespace FrameJam\Commands;

use FrameJam\Core\Database\MigrationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('migrate')
            ->setDescription('Executa todas as migrações pendentes');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Executando migrações...');

        try {
            $migrationManager = new MigrationManager(__DIR__ . '/../Database/Migrations');
            $migrationManager->run();
            
            $io->success('Todas as migrações foram executadas com sucesso!');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $io->error('Erro ao executar migrações: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 