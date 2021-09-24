<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\DefinitionResolver;
use Scruwi\Container\Exceptions\ContainerException;
use Tests\Autowired\ClassCreatedByDefinition;
use Tests\Autowired\SomeClassImplementsInterface1;
use Tests\Autowired\SomeInterface;
use Tests\Definitions\CustomCreatedClassDefinition;
use Tests\Definitions\CustomInterfaceDefinition;

/**
 * @covers \Scruwi\Container\DefinitionResolver
 */
class DefinitionResolverTest extends TestCase
{
    public function testCreateDefinitionWithUnknownClass(): void
    {
        $this->expectException(ContainerException::class);
        new DefinitionResolver(['Fake\\Definition']);
    }

    public function testCanOnEmptyDefinitions(): void
    {
        $resolver = new DefinitionResolver();
        self::assertFalse($resolver->can(ClassCreatedByDefinition::class));
    }

    public function testCanOnConfiguredDefinition(): void
    {
        $resolver = new DefinitionResolver([CustomCreatedClassDefinition::class]);
        self::assertTrue($resolver->can(ClassCreatedByDefinition::class));
    }

    public function testCanOnDefinitionPushedByHand(): void
    {
        $resolver = new DefinitionResolver();
        $resolver->push('class', new CustomCreatedClassDefinition());
        self::assertTrue($resolver->can('class'));
    }

    public function testGetOnEmptyDefinitions(): void
    {
        $this->expectException(ContainerException::class);
        $resolver = new DefinitionResolver();
        $resolver->get(ClassCreatedByDefinition::class);
    }

    public function testGenUnknownId(): void
    {
        $this->expectException(ContainerException::class);
        $resolver = new DefinitionResolver([CustomCreatedClassDefinition::class]);
        $resolver->get(SomeClassImplementsInterface1::class);
    }

    /**
     * @dataProvider realDefinitionsDataProvider
     */
    public function testGetOnConfiguredDefinition($expect, string $id): void
    {
        $resolver = new DefinitionResolver([
            CustomCreatedClassDefinition::class,
            CustomInterfaceDefinition::class,
        ]);

        self::assertInstanceOf($expect, $resolver->get($id));
    }

    public function realDefinitionsDataProvider(): iterable
    {
        return [
            [CustomCreatedClassDefinition::class, ClassCreatedByDefinition::class],
            [CustomInterfaceDefinition::class, SomeInterface::class],
        ];
    }

    public function testGetOnDefinitionPushedByHand(): void
    {
        $resolver = new DefinitionResolver([
            CustomCreatedClassDefinition::class,
        ]);
        $resolver->push('class', new CustomCreatedClassDefinition());
        $resolver->push('another-class', new CustomCreatedClassDefinition());

        $instance1 = $resolver->get('class');
        $instance2 = $resolver->get('another-class');
        $instance3 = $resolver->get(ClassCreatedByDefinition::class);

        self::assertInstanceOf(CustomCreatedClassDefinition::class, $instance1);
        self::assertInstanceOf(CustomCreatedClassDefinition::class, $instance2);
        self::assertSame($instance1, $instance2);
        self::assertSame($instance1, $instance3);
    }
}
