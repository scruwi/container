<?php

namespace Scruwi\Container\Interfaces;

interface DefinitionResolverInterface
{
    public function can(string $id): bool;

    public function get(string $id): DefinitionInterface;

    public function push(string $id, DefinitionInterface $definition): void;
}
