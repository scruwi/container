<?php

namespace Scruwi\Container;

use Scruwi\Container\Interfaces\ClassResolverInterface;
use Scruwi\Container\Interfaces\DefinitionInterface;

class ClassResolver implements ClassResolverInterface
{
    private array $namespaces;

    public function __construct(array $namespaces = [])
    {
        $this->namespaces = $namespaces;
    }

    /**
     * @codeCoverageIgnore
     */
    public static function buildDefinition(string $id): DefinitionInterface
    {
        return new ReflectionClassDefinition($id);
    }

    public function can(string $id): bool
    {
        foreach ($this->namespaces as $namespace) {
            if (strpos($id, $namespace) === 0) {
                return true;
            }
        }

        return false;
    }
}
