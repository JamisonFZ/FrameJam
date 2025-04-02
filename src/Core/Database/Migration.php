<?php

namespace FrameJam\Core\Database;

use PDO;

abstract class Migration
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Model::getConnection();
    }

    abstract public function up(): void;
    abstract public function down(): void;

    protected function createTable(string $table, array $columns): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$table} (";
        $sql .= implode(', ', array_map(fn($col) => "{$col['name']} {$col['type']}", $columns));
        $sql .= ')';

        $this->db->exec($sql);
    }

    protected function dropTable(string $table): void
    {
        $this->db->exec("DROP TABLE IF EXISTS {$table}");
    }

    protected function addColumn(string $table, string $column, string $type): void
    {
        $this->db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$type}");
    }

    protected function dropColumn(string $table, string $column): void
    {
        $this->db->exec("ALTER TABLE {$table} DROP COLUMN {$column}");
    }

    protected function addIndex(string $table, string $column, string $name = null): void
    {
        $name = $name ?? "{$table}_{$column}_index";
        $this->db->exec("CREATE INDEX {$name} ON {$table} ({$column})");
    }

    protected function dropIndex(string $table, string $name): void
    {
        $this->db->exec("DROP INDEX {$name} ON {$table}");
    }

    protected function addForeignKey(string $table, string $column, string $referenceTable, string $referenceColumn, string $name = null): void
    {
        $name = $name ?? "{$table}_{$column}_foreign";
        $this->db->exec("ALTER TABLE {$table} ADD CONSTRAINT {$name} FOREIGN KEY ({$column}) REFERENCES {$referenceTable} ({$referenceColumn})");
    }

    protected function dropForeignKey(string $table, string $name): void
    {
        $this->db->exec("ALTER TABLE {$table} DROP FOREIGN KEY {$name}");
    }
} 