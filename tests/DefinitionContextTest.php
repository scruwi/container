<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Scruwi\Container\Container;
use Scruwi\Container\DefinitionResolver;
use Tests\Autowired\ClassCreatedByDefinition;
use Tests\Autowired\ClassWithDefinitionDependency;
use Tests\Definitions\CustomCreatedClassDefinition;

/**
 * @covers \Scruwi\Container\BuildContext
 */
class DefinitionContextTest extends TestCase
{
    public function testContextDataOnSelf(): void
    {
        $container = new Container([
            new DefinitionResolver([CustomCreatedClassDefinition::class]),
        ]);

        /** @var ClassCreatedByDefinition $instance */
        $instance = $container->get(ClassCreatedByDefinition::class);

        self::assertInstanceOf(ClassCreatedByDefinition::class, $instance);
        self::assertNull($instance->targetClass);
        self::assertNull($instance->parameterName);
    }

    public function testContextDataOnDependency(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new DefinitionResolver([CustomCreatedClassDefinition::class]),
        ]);

        /** @var ClassWithDefinitionDependency $instance */
        $instance = $container->get(ClassWithDefinitionDependency::class);
        $dependency = $instance->getDependency();

        self::assertSame(ClassWithDefinitionDependency::class, $dependency->targetClass);
        self::assertSame('dependency', $dependency->parameterName);
    }
}
