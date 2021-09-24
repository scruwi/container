<?php

namespace Scruwi\Container\Interfaces;

use Psr\Container\ContainerInterface;
use Scruwi\Container\BuildContext;

interface ParameterResolverInterface
{
    public function __invoke(\ReflectionParameter $parameter, ?BuildContext $context, ContainerInterface $container);

    public function has(\ReflectionParameter $parameter): bool;

    public function resolve(string $parameterName);
}
