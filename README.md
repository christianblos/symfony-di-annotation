## What's this?

This library is an extension of the [Symfony DependencyInjection Component](https://github.com/symfony/dependency-injection).
It allows you to configure the DI Container using annotations directly in your classes.

## Set up

### 1. Install via composer:

`composer require christianblos/symfony-di-annotation`

### 2. Add compiler pass to the ContainerBuilder

```php
<?php
use Symfony\Component\DependencyInjection\Annotation\Compiler\AnnotationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addCompilerPass(AnnotationPass::createDefault($srcDirs));
```

(see [symfony documentation](http://symfony.com/doc/current/components/dependency_injection.html) for more information about the ContainerBuilder)

## Basic Usage

Just add the `@Service` annotation to all of your services and they will be registered to the DIC automatically:

```php
<?php
use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service
 */
class SomeRepository
{

}
```

```php
<?php
use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(public=true)
 */
class SomeService
{
    public function __construct(SomeRepository $repo)
    {
        // $repo will be injected automatically
    }
}
```

Now you can simply retrieve the service from the container:

```php
$someService = $container->get(SomeService::class);
```

## Inject params

You can also inject params by adding it to the annotation:

```
<?php
use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     inject={
 *         "someParam"="%foo%"
 *     }
 * )
 */
class SomeService
{
    public function __construct($someParam)
    {
        // - "someParam" ist the name of the variable
        // - "%foo%" means you want to inject the "foo" parameter from the container
    }
}
```

## More examples

You can find some examples in the [examples folder](https://github.com/christianblos/symfony-di-annotation/tree/master/examples).
