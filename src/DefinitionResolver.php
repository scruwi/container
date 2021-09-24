<?php

namespace Scruwi\Container;

use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\Interfaces\DefinitionInterface;
use Scruwi\Container\Interfaces\DefinitionResolverInterface;

class DefinitionResolver implements DefinitionResolverInterface
{
    private array $config = [];

    private array $instancesById = [];

    private array $instancesByClassName = [];

    public function __construct(array $config = [])
    {
        /** @var DefinitionInterface|string $class */
        foreach ($config as $class) {
            if (!class_exists($class)) {
                throw new ContainerException("Not found definition class {$class}");
            }
            $this->config[$class::getId()] = $class;
        }
    }

    public function can(string $id): bool
    {
        return $this->hasInstance($id) || $this->knownDefinition($id);
    }

    private function hasInstance(string $id): bool
    {
        return array_key_exists($id, $this->instancesById);
    }

    private function knownDefinition(string $id): bool
    {
        return array_key_exists($id, $this->config);
    }

    public function get(string $id): DefinitionInterface
    {
        if ($this->hasInstance($id)) {
            return $this->instancesById[$id];
        }

        if ($this->knownDefinition($id)) {
            $instance = $this->buildInstance($id);
            $this->push($id, $instance);
            return $instance;
        }

        throw new ContainerException("Definition not found for {$id}");
    }

    private function buildInstance(string $id): DefinitionInterface
    {
        $class = $this->config[$id];
        if (array_key_exists($class, $this->instancesByClassName)) {
            return $this->instancesByClassName[$class];
        }

        return new $class;
    }

    public function push(string $id, DefinitionInterface $definition): void
    {
        $class = get_class($definition);
        if (!array_key_exists($class, $this->instancesByClassName)) {
            $this->instancesByClassName[$class] = $definition;
        }

        $this->instancesById[$id] =& $this->instancesByClassName[$class];
    }
}
