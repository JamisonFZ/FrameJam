<?php

namespace FrameJam\Core\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

abstract class Migration
{
    protected $schema;

    public function __construct()
    {
        $this->schema = Capsule::schema();
    }

    abstract public function up(): void;
    abstract public function down(): void;

    protected function createTable(string $table, \Closure $callback): void
    {
        $this->schema->create($table, $callback);
    }

    protected function dropTable(string $table): void
    {
        $this->schema->dropIfExists($table);
    }

    protected function table(string $table, \Closure $callback): void
    {
        $this->schema->table($table, $callback);
    }
} 