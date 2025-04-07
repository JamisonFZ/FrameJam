<?php

namespace FrameJam\Core\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class MigrationManager
{
    private string $migrationsPath;
    private string $migrationsTable = 'migrations';

    public function __construct(string $migrationsPath = null)
    {
        $this->migrationsPath = $migrationsPath ?? __DIR__ . '/../../database/migrations';
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        if (!Capsule::schema()->hasTable($this->migrationsTable)) {
            Capsule::schema()->create($this->migrationsTable, function ($table) {
                $table->increments('id');
                $table->string('migration');
                $table->integer('batch');
            });
        }
    }

    public function migrate(): void
    {
        $files = $this->getMigrationFiles();
        $batch = $this->getNextBatchNumber();
        $migrated = 0;

        foreach ($files as $file) {
            $migration = $this->getMigrationName($file);
            
            if (!$this->hasMigrated($migration)) {
                $this->runMigration($file, $migration, $batch);
                $migrated++;
            }
        }

        echo "Migrated {$migrated} migrations.\n";
    }

    public function rollback(): void
    {
        $batch = $this->getLastBatchNumber();
        $migrations = $this->getMigrationsForBatch($batch);
        $rolledBack = 0;

        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigration($migration);
            $rolledBack++;
        }

        echo "Rolled back {$rolledBack} migrations.\n";
    }

    public function reset(): void
    {
        $batches = $this->getMigrationBatches();
        
        foreach (array_reverse($batches) as $batch) {
            $this->rollbackBatch($batch);
        }
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        return $files;
    }

    private function getMigrationName(string $file): string
    {
        return basename($file, '.php');
    }

    private function hasMigrated(string $migration): bool
    {
        return Capsule::table($this->migrationsTable)
            ->where('migration', $migration)
            ->exists();
    }

    private function runMigration(string $file, string $migration, int $batch): void
    {
        require_once $file;
        $class = $this->getMigrationClass($migration);
        $instance = new $class();
        
        $instance->up();
        
        Capsule::table($this->migrationsTable)->insert([
            'migration' => $migration,
            'batch' => $batch
        ]);
        
        echo "Migrated: {$migration}\n";
    }

    private function rollbackMigration(string $migration): void
    {
        $file = $this->migrationsPath . '/' . $migration . '.php';
        require_once $file;
        
        $class = $this->getMigrationClass($migration);
        $instance = new $class();
        
        $instance->down();
        
        Capsule::table($this->migrationsTable)
            ->where('migration', $migration)
            ->delete();
        
        echo "Rolled back: {$migration}\n";
    }

    private function rollbackBatch(int $batch): void
    {
        $migrations = $this->getMigrationsForBatch($batch);
        
        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    private function getMigrationClass(string $migration): string
    {
        $parts = explode('_', $migration);
        array_shift($parts); // Remove timestamp
        
        $className = '';
        foreach ($parts as $part) {
            $className .= ucfirst($part);
        }
        
        return $className;
    }

    private function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    private function getLastBatchNumber(): int
    {
        $lastBatch = Capsule::table($this->migrationsTable)
            ->max('batch');
            
        return $lastBatch ?: 0;
    }

    private function getMigrationsForBatch(int $batch): array
    {
        return Capsule::table($this->migrationsTable)
            ->where('batch', $batch)
            ->pluck('migration')
            ->toArray();
    }

    private function getMigrationBatches(): array
    {
        return Capsule::table($this->migrationsTable)
            ->distinct()
            ->pluck('batch')
            ->toArray();
    }
} 