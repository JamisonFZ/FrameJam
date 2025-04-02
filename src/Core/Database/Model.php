<?php

namespace FrameJam\Core\Database;

use PDO;
use PDOException;
use FrameJam\Core\Application;

abstract class Model
{
    protected static ?PDO $connection = null;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->table = $this->getTableName();
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = Application::getInstance()->getConfig()->get('database');
            
            try {
                self::$connection = new PDO(
                    "{$config['driver']}:host={$config['host']};dbname={$config['database']}",
                    $config['username'],
                    $config['password'],
                    $config['options'] ?? []
                );
            } catch (PDOException $e) {
                throw new \Exception("Database connection failed: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    protected function getTableName(): string
    {
        if (isset($this->table)) {
            return $this->table;
        }

        $className = (new \ReflectionClass($this))->getShortName();
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $className)) . 's';
    }

    public static function find(int $id): ?self
    {
        $instance = new static();
        $stmt = self::getConnection()->prepare(
            "SELECT * FROM {$instance->table} WHERE {$instance->primaryKey} = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new static($result) : null;
    }

    public static function all(): array
    {
        $instance = new static();
        $stmt = self::getConnection()->query("SELECT * FROM {$instance->table}");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($result) => new static($result), $results);
    }

    public function save(): bool
    {
        $attributes = array_intersect_key($this->attributes, array_flip($this->fillable));

        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update($attributes);
        }

        return $this->insert($attributes);
    }

    protected function insert(array $attributes): bool
    {
        $columns = implode(', ', array_keys($attributes));
        $values = implode(', ', array_fill(0, count($attributes), '?'));
        
        $stmt = self::getConnection()->prepare(
            "INSERT INTO {$this->table} ($columns) VALUES ($values)"
        );

        $result = $stmt->execute(array_values($attributes));
        
        if ($result) {
            $this->attributes[$this->primaryKey] = self::getConnection()->lastInsertId();
        }

        return $result;
    }

    protected function update(array $attributes): bool
    {
        $set = implode(' = ?, ', array_keys($attributes)) . ' = ?';
        
        $stmt = self::getConnection()->prepare(
            "UPDATE {$this->table} SET $set WHERE {$this->primaryKey} = ?"
        );

        $values = array_values($attributes);
        $values[] = $this->attributes[$this->primaryKey];

        return $stmt->execute($values);
    }

    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        $stmt = self::getConnection()->prepare(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?"
        );

        return $stmt->execute([$this->attributes[$this->primaryKey]]);
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function toArray(): array
    {
        return array_diff_key($this->attributes, array_flip($this->hidden));
    }
} 