<?php

namespace Tests\Definitions;

use Scruwi\Container\BuildContext;
use Scruwi\Container\Container;
use Scruwi\Container\Interfaces\DefinitionInterface;
use Scruwi\Container\ParameterResolver;
use Tests\Autowired\ClassWithInterfaceDependency;
use Tests\Autowired\SomeClassImplementsInterface1;
use Tests\Autowired\SomeClassImplementsInterface2;
use Tests\Autowired\SomeInterface;

class CustomInterfaceDefinition implements DefinitionInterface
{
    public static function getId(): string
    {
        return SomeInterface::class;
    }

    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object
    {
        if ($context->getTargetClass() === ClassWithInterfaceDependency::class) {
            return new SomeClassImplementsInterface2();
        }

        return new SomeClassImplementsInterface1();
    }
}
