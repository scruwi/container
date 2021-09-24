<?php

namespace Scruwi\Container\Interfaces;

interface ClassResolverInterface
{
    public static function buildDefinition(string $id): DefinitionInterface;

    public function can(string $id): bool;
}
