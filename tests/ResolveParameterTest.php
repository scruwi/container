<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Scruwi\Container\Container;
use Scruwi\Container\Exceptions\ContainerException;
use Scruwi\Container\ParameterResolver;
use Tests\Autowired\ClassWithDefinitionDependency;
use Tests\Autowired\ClassWithScalarParameters;

/**
 * @covers \Scruwi\Container\ParameterResolver
 */
class ResolveParameterTest extends TestCase
{
    public function testUnresolvedParameter(): void
    {
        $this->expectException(ContainerException::class);
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
        ]);

        $container->get(ClassWithScalarParameters::class);
    }

    public function testResolvedRootParameter(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new ParameterResolver(['bool' => true, 'string' => 'string']),
        ]);

        /** @var ClassWithScalarParameters $instance */
        $instance = $container->get(ClassWithScalarParameters::class);

        self::assertTrue($instance->bool);
        self::assertSame('string', $instance->string);
        self::assertSame('default', $instance->default);
    }

    public function testResolvedClassSpecificParameter(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
            new ParameterResolver(
                ['bool' => true, 'string' => 'string'],
                [
                    ClassWithScalarParameters::class => ['bool' => false],
                ]
            ),
        ]);

        /** @var ClassWithScalarParameters $instance */
        $instance = $container->get(ClassWithScalarParameters::class);

        self::assertFalse($instance->bool);
        self::assertSame('string', $instance->string);
        self::assertSame('default', $instance->default);
    }

    public function testObjectParameter(): void
    {
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
        ]);

        $instance = $container->get(ClassWithDefinitionDependency::class);
        self::assertInstanceOf(ClassWithDefinitionDependency::class, $instance);
    }
}
