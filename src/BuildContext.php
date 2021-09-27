<?php

namespace Scruwi\Container;

use Scruwi\Container\Exceptions\CircularReferencesException;

class BuildContext
{
    private array $path = [];

    private \ReflectionParameter $parameter;

    public function setParameter(\ReflectionParameter $parameter): void
    {
        $this->parameter = $parameter;
        $this->checkCircularDependency();
    }

    private function checkCircularDependency(): void
    {
        $lastClass = array_key_last($this->path);
        $class = $this->getTargetClass();
        if ($class !== $lastClass && array_key_exists($class, $this->path)) {
            throw new CircularReferencesException(array_keys($this->path));
        }

        $this->path[$class] = true;
    }

    public function getTargetClass(): ?string
    {
        if (empty($this->parameter)) {
            return null;
        }

        $classReflection = $this->parameter->getDeclaringClass();

        return $classReflection ? $classReflection->getName() : null;
    }

    public function getParameterName(): ?string
    {
        if (empty($this->parameter)) {
            return null;
        }

        return $this->parameter->getName();
    }
}
