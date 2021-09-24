<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\BuildContext;
use Scruwi\Container\Container;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\ParameterResolver;
use Scruwi\Container\ReflectionClassDefinition;
use Tests\Autowired\ClassWithoutConstructor;
use Tests\Autowired\ClassWithScalarParameters;

/**
 * @covers \Scruwi\Container\ReflectionClassDefinition
 */
class ReflectionClassDefinitionTest extends TestCase
{
    public function testGetId(): void
    {
        $this->expectException(\LogicException::class);
        $definition = new ReflectionClassDefinition(ClassWithoutConstructor::class);
        self::assertEquals(ClassWithoutConstructor::class, $definition::getId());
    }

    public function testCreateObjectUnknownClass(): void
    {
        $this->expectException(ContainerException::class);
        $container = new Container();
        $context = new BuildContext();
        $resolver = new ParameterResolver();
        $definition = new ReflectionClassDefinition('Fake\\Namespace\\Class');

        $definition($container, $resolver, $context);
    }

    public function testCreateObjectWithoutConstructor(): void
    {
        $container = new Container();
        $context = new BuildContext();
        $resolver = new ParameterResolver();
        $definition = new ReflectionClassDefinition(ClassWithoutConstructor::class);

        $instance = $definition($container, $resolver, $context);

        self::assertInstanceOf(ClassWithoutConstructor::class, $instance);
    }

    public function testCreateObjectWithConstructor(): void
    {
        $container = new Container();
        $context = new BuildContext();
        $resolver = new ParameterResolver(['bool' => true, 'string' => 'string']);
        $definition = new ReflectionClassDefinition(ClassWithScalarParameters::class);

        $instance = $definition($container, $resolver, $context);

        self::assertInstanceOf(ClassWithScalarParameters::class, $instance);
    }
}
