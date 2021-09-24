<?php

namespace Scruwi\Container\Interfaces;

use Scruwi\Container\BuildContext;
use Scruwi\Container\Container;
use Scruwi\Container\ParameterResolver;

interface DefinitionInterface
{
    public static function getId(): string;

    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object;
}
