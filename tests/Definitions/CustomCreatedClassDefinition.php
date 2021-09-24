<?php

namespace Tests\Definitions;

use Scruwi\Container\BuildContext;
use Scruwi\Container\Container;
use Scruwi\Container\Interfaces\DefinitionInterface;
use Scruwi\Container\ParameterResolver;
use Tests\Autowired\ClassCreatedByDefinition;

class CustomCreatedClassDefinition implements DefinitionInterface
{
    public static function getId(): string
    {
        return ClassCreatedByDefinition::class;
    }

    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object
    {
        $instance = new ClassCreatedByDefinition();
        $instance->targetClass = $context->getTargetClass();
        $instance->parameterName = $context->getParameterName();

        return $instance;
    }
}
