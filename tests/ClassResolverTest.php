<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Tests\Autowired\ClassWithoutConstructor;

/**
 * @covers \Scruwi\Container\ClassResolver
 */
class ClassResolverTest extends TestCase
{
    public function testCanOnEmptyResolver(): void
    {
        $resolver = new ClassResolver();
        self::assertFalse($resolver->can(ClassWithoutConstructor::class));
    }

    public function testCanClassOutOfNamespace(): void
    {
        $resolver = new ClassResolver(['Fake\\Namespace\\']);
        self::assertFalse($resolver->can(ClassWithoutConstructor::class));
    }

    public function testCan(): void
    {
        $resolver = new ClassResolver(['Tests\\Autowired\\']);
        self::assertTrue($resolver->can(ClassWithoutConstructor::class));
    }
}
