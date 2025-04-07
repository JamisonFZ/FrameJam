<?php

namespace FrameJam\Core\Model;

abstract class Observer
{
    public function created($model): void
    {
    }

    public function updated($model): void
    {
    }

    public function deleted($model): void
    {
    }

    public function restored($model): void
    {
    }

    public function forceDeleted($model): void
    {
    }
} 