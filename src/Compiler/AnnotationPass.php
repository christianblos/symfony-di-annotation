<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Annotation\Inject\InjectableInterface;
use Symfony\Component\DependencyInjection\Annotation\Inject\MethodAnnotationInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\TagInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Reference;

class AnnotationPass implements CompilerPassInterface
{
    /**
     * @var string[]
     */
    private $srcDirs;

    /**
     * @var ServiceFinder
     */
    private $serviceFinder;

    /**
     * @param array $srcDirs
     *
     * @return AnnotationPass
     */
    public static function createDefault(array $srcDirs)
    {
        $serviceFinder = new ServiceFinder(new FileLoader(), new AutoloadedAnnotationReader());

        return new self($srcDirs, $serviceFinder);
    }

    /**
     * @param string[]      $srcDirs
     * @param ServiceFinder $serviceFinder
     */
    public function __construct(array $srcDirs, ServiceFinder $serviceFinder)
    {
        $this->srcDirs       = $srcDirs;
        $this->serviceFinder = $serviceFinder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws ServiceNotFoundException
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($this->srcDirs as $srcDir) {
            $container->addResource(new DirectoryResource($srcDir));
        }

        $services = $this->serviceFinder->findServiceAnnotations($this->srcDirs);

        foreach ($services as $id => $service) {
            $service = $this->modifyServiceByMethodAnnotations($service, $container);

            $container->setDefinition($id, $this->createServiceDefinition($service));
        }

        // resolve arguments after all services are added, so we know all available services here
        foreach ($services as $id => $service) {
            $definition = $container->getDefinition($id);

            if (!$service->factoryClass) {
                $definition->setArguments($this->getConstructorArguments($service, $container));
            }
        }
    }

    /**
     * @param Service $service
     *
     * @return Definition
     * @throws InvalidArgumentException
     */
    protected function createServiceDefinition(Service $service)
    {
        $className = $service->getClass()->getName();

        $definition = new Definition($className);
        $definition->setAutowired(true);
        $definition->setPublic($service->public);
        $definition->setShared($service->shared);
        $definition->setLazy($service->lazy);

        if ($service->factoryClass) {
            $definition->setFactory([
                $this->resolveValue($service->factoryClass),
                $service->factoryMethod,
            ]);

            if ($service->factoryArguments) {
                $definition->setArguments(array_map([$this, 'resolveValue'], $service->factoryArguments));
            }
        }

        $this->addTags($definition, $service);

        return $definition;
    }

    /**
     * @param Service          $service
     * @param ContainerBuilder $container
     *
     * @return Service
     */
    private function modifyServiceByMethodAnnotations(Service $service, ContainerBuilder $container)
    {
        foreach ($service->getAllMethodAnnotations() as $method => $methodAnnotations) {
            /** @var MethodAnnotationInterface[] $methodAnnotations */
            foreach ($methodAnnotations as $methodAnnotation) {
                $service = $methodAnnotation->modifyService($service, $method, $container);
            }
        }

        return $service;
    }

    /**
     * @param Definition $definition
     * @param Service    $service
     *
     * @throws InvalidArgumentException
     */
    private function addTags(Definition $definition, Service $service)
    {
        foreach ($service->tags as $tag) {
            if ($tag instanceof TagInterface) {
                $definition->addTag($tag->getTagName(), $tag->getTagAttributes());
            } elseif (is_string($tag)) {
                $definition->addTag($tag);
            } elseif (is_array($tag)) {
                if (!isset($tag['name'])) {
                    throw new InvalidArgumentException(
                        sprintf('tag must have a "name" defined in service %s', $service->id)
                    );
                }
                $tagName = $tag['name'];
                unset($tag['name']);
                $definition->addTag($tagName, $tag);
            }
        }
    }

    /**
     * @param Service          $service
     * @param ContainerBuilder $container
     *
     * @return mixed[] Indexed by param index
     */
    private function getConstructorArguments(Service $service, ContainerBuilder $container)
    {
        if (!is_array($service->inject)) {
            return [];
        }

        $class       = $service->getClass();
        $constructor = $class->getConstructor();

        if ($constructor === null) {
            return [];
        }

        return $this->resolveMethodArguments($constructor, $service->inject, $container);
    }

    /**
     * @param ReflectionMethod $method
     * @param array            $injects
     * @param ContainerBuilder $container
     *
     * @return mixed[] Indexed by param index
     */
    private function resolveMethodArguments(ReflectionMethod $method, array $injects, ContainerBuilder $container)
    {
        $arguments = [];

        foreach ($method->getParameters() as $idx => $param) {
            if (isset($injects[$param->name])) {
                $arguments[$idx] = $this->resolveMethodParam(
                    $injects[$param->name],
                    $param,
                    $container
                );
            }
        }

        return $arguments;
    }

    /**
     * @param string              $value
     * @param ReflectionParameter $param
     * @param ContainerBuilder    $container
     *
     * @return mixed
     */
    private function resolveMethodParam($value, ReflectionParameter $param, ContainerBuilder $container)
    {
        if ($value instanceof InjectableInterface) {
            return $value->getArgument($param, $container);
        }

        return $this->resolveValue($value);
    }

    /**
     * @param string $value
     *
     * @return mixed
     */
    private function resolveValue($value)
    {
        if (strpos($value, '%') === 0) {
            return $value;
        }

        if (strpos($value, '@') === 0) {
            return new Reference(substr($value, 1));
        }

        return $value;
    }
}
