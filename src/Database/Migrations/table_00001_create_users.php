<?php

namespace FrameJam\Database\Migrations;

use FrameJam\Core\Database\Migration;

class table_00001_create_users extends Migration
{
    public function getTableName(): string
    {
        return 'users';
    }

    public function up(): void
    {
        $this->schema
            ->id()
            ->string('name')
            ->string('email')->unique('email')
            ->string('password')
            ->string('remember_token')->nullable()
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        $this->schema->drop();
    }
} 