<?php

namespace FrameJam\Commands;

use FrameJam\Core\Database\Migration;
use ReflectionClass;

class MigrateCommand
{
    private string $migrationsPath;

    public function __construct()
    {
        $this->migrationsPath = __DIR__ . '/../Database/Migrations';
    }

    public function run(): void
    {
        $files = glob($this->migrationsPath . '/*.php');
        
        foreach ($files as $file) {
            $className = 'FrameJam\\Database\\Migrations\\' . basename($file, '.php');
            
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                
                if ($reflection->isSubclassOf(Migration::class)) {
                    $migration = new $className();
                    $migration->run();
                    echo "Migração {$className} executada com sucesso!\n";
                }
            }
        }
    }

    public function rollback(): void
    {
        $files = array_reverse(glob($this->migrationsPath . '/*.php'));
        
        foreach ($files as $file) {
            $className = 'FrameJam\\Database\\Migrations\\' . basename($file, '.php');
            
            if (class_exists($className)) {
                $reflection = new ReflectionClass($className);
                
                if ($reflection->isSubclassOf(Migration::class)) {
                    $migration = new $className();
                    $migration->rollback();
                    echo "Migração {$className} revertida com sucesso!\n";
                }
            }
        }
    }
} 