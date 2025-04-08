<?php

namespace FrameJam\Core\Database;

use PDO;
use ReflectionClass;

class MigrationManager
{
    private Database $db;
    private string $migrationsPath;
    private string $migrationsTable = 'migrations';

    public function __construct(string $migrationsPath)
    {
        $this->db = Database::getInstance();
        $this->migrationsPath = $migrationsPath;
        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255) NOT NULL,
            batch INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->db->query($sql);
    }

    public function run(): void
    {
        $files = $this->getMigrationFiles();
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migration = $this->getMigrationInstance($file);

            if ($migration) {
                $this->runMigration($migration, $batch);
            }
        }
    }

    public function rollback(): void
    {
        $batch = $this->getLastBatchNumber();
        $migrations = $this->getMigrationsForBatch($batch);

        foreach (array_reverse($migrations) as $migration) {
            $this->rollbackMigration($migration);
        }
    }

    public function reset(): void
    {
        while ($batch = $this->getLastBatchNumber()) {
            $this->rollback();
        }
    }

    public function refresh(): void
    {
        $this->reset();
        $this->run();
    }

    private function getMigrationFiles(): array
    {
        $files = glob($this->migrationsPath . '/*.php');
        $ran = $this->getRanMigrations();

        return array_filter($files, function ($file) use ($ran) {
            return !in_array(basename($file, '.php'), $ran);
        });
    }

    private function getRanMigrations(): array
    {
        $sql = "SELECT migration FROM {$this->migrationsTable} ORDER BY id";
        $result = $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);

        return $result ?: [];
    }

    private function getMigrationInstance(string $file): ?Migration
    {
        $className = 'FrameJam\\Database\\Migrations\\' . basename($file, '.php');

        if (class_exists($className)) {
            $reflection = new ReflectionClass($className);

            if ($reflection->isSubclassOf(Migration::class)) {
                return new $className();
            }
        }

        return null;
    }

    private function runMigration(Migration $migration, int $batch): void
    {
        $migration->run();
        $this->logMigration($migration, $batch);
    }

    private function rollbackMigration(array $migration): void
    {
        $instance = $this->getMigrationInstance($this->migrationsPath . '/' . $migration['migration'] . '.php');

        if ($instance) {
            $instance->rollback();
            $this->deleteMigration($migration['id']);
        }
    }

    private function logMigration(Migration $migration, int $batch): void
    {
        $sql = "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)";
        $this->db->query($sql, [$migration->getTableName(), $batch]);
    }

    private function deleteMigration(int $id): void
    {
        $sql = "DELETE FROM {$this->migrationsTable} WHERE id = ?";
        $this->db->query($sql, [$id]);
    }

    private function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    private function getLastBatchNumber(): int
    {
        $sql = "SELECT MAX(batch) as batch FROM {$this->migrationsTable}";
        $result = $this->db->query($sql)->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['batch'] ?? 0);
    }

    private function getMigrationsForBatch(int $batch): array
    {
        $sql = "SELECT * FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id";
        return $this->db->query($sql, [$batch])->fetchAll(PDO::FETCH_ASSOC);
    }
} 