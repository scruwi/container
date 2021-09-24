<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Scruwi\Container\Container;
use Scruwi\Container\DefinitionResolver;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\Exceptions\ContainerNotFoundException;
use Scruwi\Container\ParameterResolver;
use Tests\Autowired\ClassCreatedByDefinition;
use Tests\Autowired\ClassWithAutowiredDependency;
use Tests\Autowired\ClassWithDefinitionDependency;
use Tests\Autowired\ClassWithoutConstructor;
use Tests\Autowired\ClassWithoutDependencies;
use Tests\Autowired\ClassWithScalarParameters;
use Tests\Autowired\LeafReferenceClassNode;
use Tests\Definitions\CustomCreatedClassDefinition;

/**
 * @covers \Scruwi\Container\Container
 */
class ContainerTest extends TestCase
{
    public function testHasNotInEmptyContainer(): void
    {
        $container = new Container();

        self::assertFalse($container->has('another-class'));
    }

    public function testHasNotButAnotherDefinitionIs(): void
    {
        $container = new Container();

        $definition = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition);

        self::assertFalse($container->has('another-class'));
    }

    public function testHasNotInAutowired(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
        ]);

        self::assertFalse($container->has(CustomCreatedClassDefinition::class));
    }

    public function testHasWithCustomDefinition(): void
    {
        $container = new Container();

        $definition = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition);

        self::assertTrue($container->has('class'));
    }

    public function testHasInAutowired(): void
    {
        $container = new Container([new ClassResolver(['Tests\\Autowired\\'])]);

        self::assertTrue($container->has(ClassWithoutConstructor::class));
    }

    public function testGetFromEmptyContainer(): void
    {
        $this->expectException(ContainerNotFoundException::class);

        $container = new Container();
        $container->get('another-class');
    }

    public function testGetNotInCustomDefinition(): void
    {
        $this->expectException(ContainerNotFoundException::class);
        $container = new Container();

        $definition = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition);
        $container->get('another-class');
    }

    public function testGetNotAutowired(): void
    {
        $this->expectException(ContainerNotFoundException::class);
        $container = new Container([new ClassResolver(['Tests\\Autowired\\'])]);

        $container->get(CustomCreatedClassDefinition::class);
    }

    public function testGetByCustomDefinition(): void
    {
        $container = new Container();

        $definition = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition);
        $instance1 = $container->get('class');
        $instance2 = $container->get('class');

        self::assertInstanceOf(ClassCreatedByDefinition::class, $instance1);
        self::assertSame($instance1, $instance2);
    }

    /**
     * @dataProvider realClassesDataProvider
     */
    public function testGetWithAutowired(string $expected): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new ParameterResolver(['bool' => true, 'string' => 'string']),
            new DefinitionResolver([CustomCreatedClassDefinition::class]),
        ]);

        $instance1 = $container->get($expected);
        $instance2 = $container->get($expected);

        self::assertInstanceOf($expected, $instance1);
        self::assertSame($instance1, $instance2);
    }

    public function realClassesDataProvider(): iterable
    {
        return [
            [ClassWithoutConstructor::class],
            [ClassWithoutDependencies::class],
            [ClassWithAutowiredDependency::class],
            [ClassWithDefinitionDependency::class],
            [LeafReferenceClassNode::class],
            [ClassWithScalarParameters::class],
        ];
    }

    public function testAddSameDefinitionTwice(): void
    {
        $container = new Container();

        $definition = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition);
        $container->addDefinition('another-class', $definition);

        self::assertTrue($container->has('class'));
        self::assertTrue($container->has('another-class'));
    }

    public function testReplaceDefinition(): void
    {
        $this->expectException(ContainerException::class);
        $container = new Container();

        $definition1 = new CustomCreatedClassDefinition();
        $container->addDefinition('class', $definition1);
        $definition2 = clone $definition1;
        $container->addDefinition('class', $definition2);
    }

    public function testPushSameObjectTwice(): void
    {
        $container = new Container();
        $instance = new \stdClass();

        $container->push('class', $instance);
        $container->push('class', $instance);
        $container->push('class-another', $instance);

        self::assertTrue($container->has('class'));
        self::assertTrue($container->has('class-another'));
    }

    public function testReplaceObject(): void
    {
        $this->expectException(ContainerException::class);
        $container = new Container();
        $instance1 = new \stdClass();
        $instance2 = clone $instance1;

        $container->push('class', $instance1);
        $container->push('class', $instance2);
    }
}
