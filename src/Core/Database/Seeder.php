<?php

namespace FrameJam\Core\Database;

use PDO;

abstract class Seeder
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Model::getConnection();
    }

    abstract public function run(): void;

    protected function insert(string $table, array $data): void
    {
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$values})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));
    }

    protected function insertMany(string $table, array $data): void
    {
        if (empty($data)) {
            return;
        }

        $columns = implode(', ', array_keys($data[0]));
        $values = implode(', ', array_fill(0, count($data), '(' . implode(', ', array_fill(0, count($data[0]), '?')) . ')'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES {$values}";
        $stmt = $this->db->prepare($sql);
        
        $values = [];
        foreach ($data as $row) {
            $values = array_merge($values, array_values($row));
        }
        
        $stmt->execute($values);
    }

    protected function truncate(string $table): void
    {
        $this->db->exec("TRUNCATE TABLE {$table}");
    }
} 