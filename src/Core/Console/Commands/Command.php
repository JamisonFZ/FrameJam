<?php

namespace FrameJam\Core\Console\Commands;

abstract class Command
{
    abstract public function execute(array $args = []): void;
    abstract public function getDescription(): string;
    abstract public function getUsage(): string;
} 