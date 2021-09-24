<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Scruwi\Container\Container;
use Scruwi\Container\DefinitionResolver;
use Tests\Autowired\ClassWithInterfaceDependency;
use Tests\Autowired\SomeClassImplementsInterface1;
use Tests\Autowired\SomeClassImplementsInterface2;
use Tests\Autowired\SomeInterface;
use Tests\Definitions\CustomInterfaceDefinition;

/**
 * @covers \Scruwi\Container\ReflectionClassDefinition
 */
class InterfaceDefinitionTest extends TestCase
{
    public function testGetInterfaceMissedDefinition(): void
    {
        $this->expectException(\Error::class);
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
        ]);

        $container->get(SomeInterface::class);
    }

    public function testGetInterfaceByDefinition(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new DefinitionResolver([CustomInterfaceDefinition::class]),
        ]);

        $instance = $container->get(SomeInterface::class);

        self::assertInstanceOf(SomeClassImplementsInterface1::class, $instance);
    }

    public function testGetClassWithInterfaceDependency(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new DefinitionResolver([CustomInterfaceDefinition::class]),

        ]);

        /** @var ClassWithInterfaceDependency $instance */
        $instance = $container->get(ClassWithInterfaceDependency::class);

        self::assertInstanceOf(SomeClassImplementsInterface2::class, $instance->dependency);
    }
}
