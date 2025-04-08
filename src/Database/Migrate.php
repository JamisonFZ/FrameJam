<?php

namespace FrameJam\Database;

use FrameJam\Core\Database\Migration;
use FrameJam\Database\Migrations\table_00001_create_users;

class Migrate
{
    private array $migrations = [
        table_00001_create_users::class,
        // Adicione novas migrações aqui
    ];

    public function run(): void
    {
        foreach ($this->migrations as $migrationClass) {
            /** @var Migration $migration */
            $migration = new $migrationClass();
            $migration->run();
            
            echo "Migração {$migrationClass} executada com sucesso!\n";
        }
    }

    public function rollback(): void
    {
        foreach (array_reverse($this->migrations) as $migrationClass) {
            /** @var Migration $migration */
            $migration = new $migrationClass();
            $migration->rollback();
            
            echo "Rollback da migração {$migrationClass} executado com sucesso!\n";
        }
    }
} 