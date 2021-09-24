<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Scruwi\Container\ClassResolver;
use Scruwi\Container\Container;
use Scruwi\Container\Exceptions\CircularReferencesException;
use Tests\Autowired\CircularReferenceClass1;
use Tests\Autowired\CircularReferenceClass2;
use Tests\Autowired\CircularReferenceClass3;

/**
 * @covers \Scruwi\Container\Container
 * @covers \Scruwi\Container\BuildContext
 * @covers \Scruwi\Container\Exceptions\CircularReferencesException
 */
class CircularReferenceTest extends TestCase
{
    /**
     * @dataProvider classesWithCrossDependencyDataProvider
     */
    public function testGetCircularReference(string $class): void
    {
        $exception = new CircularReferencesException([
            CircularReferenceClass1::class,
            CircularReferenceClass2::class,
            CircularReferenceClass3::class,
        ]);
        $this->expectExceptionObject($exception);
        $container = new Container([
            new ClassResolver(['Tests\\Autowired\\']),
        ]);

        $container->get($class);
    }

    public function classesWithCrossDependencyDataProvider(): iterable
    {
        return [
            [CircularReferenceClass1::class],
            [CircularReferenceClass2::class],
            [CircularReferenceClass3::class],
        ];
    }
}
