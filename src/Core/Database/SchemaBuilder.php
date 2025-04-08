<?php

namespace FrameJam\Core\Database;

class SchemaBuilder
{
    private Database $db;
    private string $table;
    private array $columns = [];
    private array $indexes = [];
    private array $foreignKeys = [];
    private string $engine = 'InnoDB';
    private string $charset = 'utf8mb4';
    private string $collation = 'utf8mb4_unicode_ci';

    public function __construct(string $table)
    {
        $this->db = Database::getInstance();
        $this->table = $table;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTableName(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function toSql(): string
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
        $sql .= implode(",\n", array_merge($this->columns, $this->indexes, $this->foreignKeys));
        $sql .= "\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collation}";
        return $sql;
    }

    public function id(string $name = 'id'): self
    {
        $this->columns[] = "`$name` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $name, int $length = 255): self
    {
        $this->columns[] = "`$name` VARCHAR($length)";
        return $this;
    }

    public function text(string $name): self
    {
        $this->columns[] = "`$name` TEXT";
        return $this;
    }

    public function integer(string $name): self
    {
        $this->columns[] = "`$name` INT";
        return $this;
    }

    public function bigInteger(string $name): self
    {
        $this->columns[] = "`$name` BIGINT";
        return $this;
    }

    public function boolean(string $name): self
    {
        $this->columns[] = "`$name` TINYINT(1)";
        return $this;
    }

    public function date(string $name): self
    {
        $this->columns[] = "`$name` DATE";
        return $this;
    }

    public function dateTime(string $name): self
    {
        $this->columns[] = "`$name` DATETIME";
        return $this;
    }

    public function timestamp(string $name): self
    {
        $this->columns[] = "`$name` TIMESTAMP";
        return $this;
    }

    public function timestamps(): self
    {
        $this->timestamp('created_at')->nullable();
        $this->timestamp('updated_at')->nullable();
        return $this;
    }

    public function nullable(): self
    {
        $this->columns[count($this->columns) - 1] .= " NULL";
        return $this;
    }

    public function default(string $value): self
    {
        $this->columns[count($this->columns) - 1] .= " DEFAULT '$value'";
        return $this;
    }

    public function unique(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $columnNames = array_map(fn($col) => "`$col`", $columns);
        $this->indexes[] = "UNIQUE KEY `" . implode('_', $columns) . "` (" . implode(', ', $columnNames) . ")";
        return $this;
    }

    public function index(string|array $columns): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $columnNames = array_map(fn($col) => "`$col`", $columns);
        $this->indexes[] = "KEY `" . implode('_', $columns) . "` (" . implode(', ', $columnNames) . ")";
        return $this;
    }

    public function foreign(string $column): ForeignKeyBuilder
    {
        return new ForeignKeyBuilder($this, $column);
    }

    public function addForeignKey(ForeignKeyBuilder $fk): self
    {
        $this->foreignKeys[] = $fk->build();
        return $this;
    }

    public function create(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n";
        $sql .= implode(",\n", array_merge($this->columns, $this->indexes, $this->foreignKeys));
        $sql .= "\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collation}";

        $this->db->query($sql);
    }

    public function drop(): void
    {
        $sql = "DROP TABLE IF EXISTS `{$this->table}`";
        $this->db->query($sql);
    }
}

class ForeignKeyBuilder
{
    private SchemaBuilder $schema;
    private string $column;
    private string $references;
    private string $on;
    private string $onDelete = 'CASCADE';
    private string $onUpdate = 'CASCADE';

    public function __construct(SchemaBuilder $schema, string $column)
    {
        $this->schema = $schema;
        $this->column = $column;
    }

    public function references(string $column): self
    {
        $this->references = $column;
        return $this;
    }

    public function on(string $table): self
    {
        $this->on = $table;
        return $this;
    }

    public function onDelete(string $action): self
    {
        $this->onDelete = $action;
        return $this;
    }

    public function onUpdate(string $action): self
    {
        $this->onUpdate = $action;
        return $this;
    }

    public function build(): string
    {
        return sprintf(
            "CONSTRAINT `fk_%s_%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE %s ON UPDATE %s",
            $this->schema->getTable(),
            $this->column,
            $this->column,
            $this->on,
            $this->references,
            $this->onDelete,
            $this->onUpdate
        );
    }
} 