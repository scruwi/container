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
        $lastClass = $this->path
            ? $this->path[array_key_last($this->path)]
            : null;

        $class = $this->getTargetClass();
        if ($class !== $lastClass && in_array($class, $this->path, true)) {
            throw new CircularReferencesException($this->path);
        }

        $this->path[] = $class;
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
