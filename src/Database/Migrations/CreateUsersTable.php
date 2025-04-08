<?php

namespace FrameJam\Database\Migrations;

use FrameJam\Core\Database\Migration;

class CreateUsersTable extends Migration
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
            ->string('email')
                ->unique('email')
            ->string('password')
            ->boolean('active')
                ->default('1')
            ->timestamps()
            ->create();
    }

    public function down(): void
    {
        $this->schema->drop();
    }
} 