My DI Container
====

[![Latest Stable Version](https://poser.pugx.org/scruwi/container/v/stable.png)](https://packagist.org/packages/scruwi/container)
[![Total Downloads](https://poser.pugx.org/scruwi/container/downloads.png)](https://packagist.org/packages/scruwi/container)
[![License](http://poser.pugx.org/scruwi/container/license)](https://packagist.org/packages/scruwi/container)
[![PHP Version Require](http://poser.pugx.org/scruwi/container/require/php)](https://packagist.org/packages/scruwi/container)
[![codecov](https://codecov.io/gh/scruwi/container/branch/main/graph/badge.svg?token=X7A4LI0F3E)](https://codecov.io/gh/scruwi/container)

It's my own implementation PSR-11 Container Interface.

## Init

```php
$container = new Container([
    new ClassResolver(['App\\Namespace1', 'App\\Namespace2']),
    new ParameterResolver(['param1' => true, 'param2' => 'string']),
    new DefinitionResolver([Definition1:class, Definition2:class]),
]);
```

## Resolvers

- [`ClassResolver`](#ClassResolver) - defines namespaces, that are allowed to autowire
- [`ParameterResolver`](#ParameterResolver) - defines scalar parameters, that can be set to constructors
- [`DefinitionResolver`](#DefinitionResolver) - defines paths to definitions

You can implement your resolvers in a project if you need them. Look at the `Interfaces` namespace.

### ClassResolver

Only classes from defined namespaces can be autowired.

### ParameterResolver

Typical usage:

```php
$rootParameters = ['param1' => true, 'param2' => 'string'];
$classSpecificParameters = [SpecificClass::class => ['param1' => false]];
$resolver = ParameterResolver($rootParameters, $classSpecificParameters);
```

Expected behaviour:

```php
class SomeClass
{
    public function __construct(bool $param1) { /* $param1 === true */ }
}
```

```php
class SpecificClass
{
    public function __construct(bool $param1) { /* $param1 === false */ }
}
```

Parameter with default value does not have to be resolved by the config:

```php
class SpecificClass
{
    public function __construct(bool $param0 = 'default') { /* $param0 === 'default' */ }
}
```

You can create a specific resolver in your way and use it in your project. Just implement `ParameterResolverInterface`
and specify it during container construct.

### DefinitionResolver

Attach definitions to container.

**Definition**, there is no more than a factory that creates an object in a specific way. There is one
specific `ReflectionClassDefinition` that constructs classes by their reflections. All autowired classes use that factory
to create their object.

You should create as many definitions in your project as you need and specify them during container construct or attach
them later by `$container->addDefinition()` method. Any definition must implement `DefinitionInterface`.

Typical definition class looks like this:

```php
class ExampleDefinition implements DefinitionInterface
{
    public static function getId(): string
    {
        return ExampleClass::class;
    }

    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object
    {
        return new ExampleClass();
    }
}
```

You can fetch some default parameters from the ParameterResolver:

```php
    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object
    {
        $param1 = $resolver->resolve('param1', ExampleClass::class);
        return new ExampleClass($param1);
    }
```

Also, you can find out from which context it was called. It may be useful for autowire interfaces:

```php
class InterfaceDefinition implements DefinitionInterface
{
    public function getId(): string
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
```

## Exceptions

- `NotFoundExceptionInterface` - for an unknown entry
- `ContainerExceptionInterface` - for all common exceptions in the container
- `CircularReferencesException` - there is a special exception for circular references in the container
