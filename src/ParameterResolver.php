<?php

namespace Scruwi\Container;

use Psr\Container\ContainerInterface;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\Interfaces\ParameterResolverInterface;

class ParameterResolver implements ParameterResolverInterface
{
    private array $config;

    private array $classParameters;

    public function __construct(array $parameters = [], array $classParameters = [])
    {
        $this->config = $parameters;
        $this->classParameters = $classParameters;
    }

    public function __invoke(\ReflectionParameter $parameter, ?BuildContext $context, ContainerInterface $container)
    {
        if (($parameterType = $parameter->getType()) && !$parameterType->isBuiltin()) {
            $id = $parameterType->getName();
            $context && $context->setParameter($parameter);

            return $container->build($id, $context);
        }

        if ($this->has($parameter)) {
            $parameterName = $parameter->getName();
            $className = $parameter->getDeclaringClass() ? $parameter->getDeclaringClass()->getName() : null;

            return $this->resolve($parameterName, $className);
        }

        try {
            return $parameter->getDefaultValue();
        } catch (\ReflectionException $e) {
            throw new ContainerException("Can't resolve parameter " . $parameter->getName());
        }
    }

    public function has(\ReflectionParameter $parameter): bool
    {
        return $this->hasRootParameter($parameter);
    }

    private function hasRootParameter(\ReflectionParameter $parameter): bool
    {
        return array_key_exists($parameter->getName(), $this->config);
    }

    public function resolve(string $parameterName, ?string $className = null)
    {
        if ($className && $this->hasClassParameter($parameterName, $className)) {
            return $this->classParameters[$className][$parameterName];
        }

        return $this->config[$parameterName];
    }

    private function hasClassParameter(string $parameterName, string $className): bool
    {
        return array_key_exists($className, $this->classParameters)
            && array_key_exists($parameterName, $this->classParameters[$className]);
    }
}
