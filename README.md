# My DI Container

My own implementation PSR-11 Container Interface.

## Init

```php
$container = new Container([
    new ClassResolver(['App\\Namespace1', 'App\\Namespace2']),
    new ParameterResolver(['param1' => true, 'param2' => 'string']),
    new DefinitionResolver(['ClassFQN\\Definition1', 'ClassFQN\\Definition2']),
]);
```

## Resolvers

- [`ClassResolver`](#ClassResolver) - define namespaces, that allowed for autowire
- [`ParameterResolver`](#ParameterResolver) - define scalar parameters, that can be set to constructors
- [`DefinitionResolver`](#DefinitionResolver) - define paths to definitions

You can implement your resolvers in a project if you need them. Look at the Interfaces namespace.

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

Parameter with default value does not need to resolve by the config:

```php
class SpecificClass
{
    public function __construct(bool $param0 = 'default') { /* $param0 === 'default' */ }
}
```

You can create a specific resolver in your way and use it in your project. Just implement ParameterResolverInterface and
specify it during container construct.

### DefinitionResolver

Attach definitions to container.

**Definition**, there is no more than a factory, that creates an object in a specific way. There is one
specific `ReflectionClassDefinition`, that constructs classes by its reflections. Any autowired classes use that factory
to create their object.

You should create as many definitions in your project, as you need and specify it during container construct or append
it later by $container->addDefinition() method. Any definition must implement DefinitionInterface.

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

You can fetch some default parameters from ParameterResolver:

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
}
    public function __invoke(Container $container, ParameterResolver $resolver, BuildContext $context): object
    {
        if ($context->getTargetClass() === ClassWithInterfaceDependency::class) {
            return new SomeClassImplementsInterface2();
        }

        return new SomeClassImplementsInterface1();
    }
```

## Exceptions

- `NotFoundExceptionInterface` - for an unknown entry
- `ContainerExceptionInterface` - for any common exceptions in the container
- `CircularReferencesException` - there is a special exception for circular references in the container