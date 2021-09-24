<?php

namespace Scruwi\Container;

use Psr\Container\ContainerInterface;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\Interfaces\DefinitionInterface;
use Scruwi\Container\Interfaces\ParameterResolverInterface;

class ReflectionClassDefinition implements DefinitionInterface
{
    private string $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public static function getId(): string
    {
        throw new \LogicException('Can\'t use this method in ReflectionClassDefinition');
    }

    /**
     * @throws ContainerException
     */
    public function __invoke(
        ContainerInterface $container,
        ParameterResolverInterface $resolver,
        BuildContext $context
    ): object {
        try {
            $reflection = new \ReflectionClass($this->className);
            $instance = $reflection->newInstanceWithoutConstructor();
        } catch (\ReflectionException $e) {
            throw new ContainerException("Class {$this->className} not found", 0, $e);
        }

        $constructor = $reflection->getConstructor();
        if (!$constructor) {
            return $instance;
        }

        $parameters = array_map(static function ($reflectionParameter) use ($resolver, $context, $container) {
            return $resolver($reflectionParameter, $context, $container);
        }, $constructor->getParameters());

        $instance->__construct(...$parameters);

        return $instance;
    }
}
