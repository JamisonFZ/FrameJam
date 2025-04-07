<?php

namespace FrameJam\Core;

class Pipeline
{
    private array $pipes = [];
    private $passable;
    private $destination;

    public function send($passable)
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $pipes)
    {
        $this->pipes = $pipes;
        return $this;
    }

    public function then(\Closure $destination)
    {
        $this->destination = $destination;
        return $this->thenReturn();
    }

    public function thenReturn()
    {
        $pipeline = array_reduce(
            array_reverse($this->pipes),
            $this->carry(),
            $this->destination
        );

        return $pipeline($this->passable);
    }

    private function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if (is_callable($pipe)) {
                    return $pipe($passable, $stack);
                }

                if (!is_object($pipe)) {
                    $pipe = new $pipe;
                }

                return $pipe->handle($passable, $stack);
            };
        };
    }
} 