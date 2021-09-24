<?php

namespace Scruwi\Container;

use Psr\Container\ContainerInterface;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\Exceptions\ContainerNotFoundException;
use Scruwi\Container\Interfaces\ClassResolverInterface;
use Scruwi\Container\Interfaces\DefinitionInterface;
use Scruwi\Container\Interfaces\DefinitionResolverInterface;
use Scruwi\Container\Interfaces\ParameterResolverInterface;

class Container implements ContainerInterface
{
    /** @var object[] */
    private array $storage = [];

    private DefinitionResolverInterface $definitionResolver;

    private ClassResolverInterface $classResolver;

    private ParameterResolverInterface $parameterResolver;

    public function __construct(array $config = [])
    {
        foreach ($config as $item) {
            if ($item instanceof DefinitionResolverInterface) {
                $this->definitionResolver = $item;
            } elseif ($item instanceof ClassResolverInterface) {
                $this->classResolver = $item;
            } elseif ($item instanceof ParameterResolverInterface) {
                $this->parameterResolver = $item;
            }
        }

        empty($this->definitionResolver) && $this->definitionResolver = new DefinitionResolver();
        empty($this->classResolver) && $this->classResolver = new ClassResolver();
        empty($this->parameterResolver) && $this->parameterResolver = new ParameterResolver();
    }

    public function addDefinition(string $id, DefinitionInterface $definition): void
    {
        if ($this->definitionResolver->can($id)) {
            throw new ContainerException("Definition for {$id} already exists");
        }

        $this->definitionResolver->push($id, $definition);
    }

    /**
     * @throws ContainerException|ContainerNotFoundException
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new ContainerNotFoundException("Unknown identifier {$id}");
        }

        if ($this->isset($id)) {
            return $this->storage[$id];
        }

        $instance = $this->build($id);
        $this->push($id, $instance);

        return $instance;
    }

    public function has(string $id): bool
    {
        return $this->isset($id) || $this->can($id);
    }

    private function isset(string $id): bool
    {
        return array_key_exists($id, $this->storage);
    }

    private function can(string $id): bool
    {
        return $this->definitionResolver->can($id) || $this->canResolve($id);
    }

    private function canResolve(string $id): bool
    {
        return $this->classResolver->can($id);
    }

    public function build(string $id, BuildContext $context = null): object
    {
        if ($this->definitionResolver->can($id)) {
            $definition = $this->definitionResolver->get($id);
        } else {
            $definition = $this->classResolver::buildDefinition($id);
        }

        $context || $context = new BuildContext();

        return $definition($this, $this->parameterResolver, $context);
    }

    /**
     * This method provide anti-pattern usage, but still may be useful in your legacy project.
     * Use it on your risk!
     *
     * @param string $id
     * @param object $instance
     */
    public function push(string $id, object $instance): void
    {
        if (array_key_exists($id, $this->storage) && $instance !== $this->get($id)) {
            throw new ContainerException("Object for {$id} already exists");
        }

        $this->storage[$id] = $instance;
    }
}
