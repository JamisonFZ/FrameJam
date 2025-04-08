<?php

namespace FrameJam\Core\Database;

abstract class Migration
{
    protected SchemaBuilder $schema;

    public function __construct()
    {
        $this->schema = new SchemaBuilder($this->getTableName());
    }

    abstract public function getTableName(): string;

    abstract public function up(): void;

    abstract public function down(): void;

    public function run(): void
    {
        $this->up();
    }

    public function rollback(): void
    {
        $this->down();
    }
} 