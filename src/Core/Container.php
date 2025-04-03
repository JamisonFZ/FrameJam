<?php

namespace FrameJam\Core;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $abstract, $concrete = null): void
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    public function singleton(string $abstract, $concrete = null): void
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null;
    }

    public function has(string $abstract): bool
    {
        return isset($this->bindings[$abstract]) || isset($this->instances[$abstract]);
    }

    public function make(string $abstract)
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        $concrete = $this->bindings[$abstract] ?? $abstract;

        if (is_callable($concrete)) {
            $instance = $concrete($this);
        } else {
            $instance = $this->build($concrete);
        }

        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $instance;
        }

        return $instance;
    }

    private function build($concrete)
    {
        // Se for uma string, tenta criar uma instância da classe
        if (is_string($concrete)) {
            // Verifica se é uma classe válida
            if (!class_exists($concrete)) {
                throw new \Exception("Class {$concrete} does not exist");
            }

            try {
                $reflector = new \ReflectionClass($concrete);
            } catch (\ReflectionException $e) {
                throw new \Exception("Class {$concrete} does not exist");
            }

            if (!$reflector->isInstantiable()) {
                throw new \Exception("Class {$concrete} is not instantiable");
            }

            $constructor = $reflector->getConstructor();

            if ($constructor === null) {
                return new $concrete();
            }

            $parameters = $constructor->getParameters();
            $dependencies = [];

            foreach ($parameters as $parameter) {
                $dependency = $parameter->getType();

                if ($dependency === null) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $dependencies[] = $parameter->getDefaultValue();
                    } else {
                        throw new \Exception("Cannot resolve class dependency {$parameter->getName()}");
                    }
                } else {
                    $dependencyName = $dependency->getName();
                    
                    // Se for um tipo primitivo, não tenta resolver
                    if (in_array($dependencyName, ['string', 'int', 'float', 'bool', 'array', 'callable', 'mixed'])) {
                        if ($parameter->isDefaultValueAvailable()) {
                            $dependencies[] = $parameter->getDefaultValue();
                        } else {
                            throw new \Exception("Cannot resolve primitive type dependency {$dependencyName}");
                        }
                    } else {
                        $dependencies[] = $this->make($dependencyName);
                    }
                }
            }

            return $reflector->newInstanceArgs($dependencies);
        }

        // Se não for uma string, retorna o valor como está
        return $concrete;
    }
} 