<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use ReflectionParameter;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Annotation\Inject\InjectableInterface;
use Symfony\Component\DependencyInjection\Annotation\Inject\MethodAnnotationInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\TagInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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
     * @param array $namespaces
     *
     * @return AnnotationPass
     */
    public static function createDefault(array $srcDirs, array $namespaces = [])
    {
        $serviceFinder = new ServiceFinder(new FileLoader(), new AutoloadedAnnotationReader($namespaces));

        return new self($srcDirs, $serviceFinder);
    }

    /**
     * @param string[]      $srcDirs
     * @param ServiceFinder $serviceFinder
     */
    public function __construct(array $srcDirs, ServiceFinder $serviceFinder)
    {
        $this->srcDirs = $srcDirs;

        $this->serviceFinder = $serviceFinder;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($this->srcDirs as $srcDir) {
            $container->addResource(new DirectoryResource($srcDir));
        }

        $services = $this->serviceFinder->findServiceAnnotations($this->srcDirs);

        foreach ($services as $id => $service) {
            $service = $this->modifyServiceByMethodAnnotations($service, $container);

            $className = $service->getClass()->getName();

            $definition = new Definition($className);
            $definition->setAutowired(true);
            $definition->setPublic($service->public);
            $definition->setLazy($service->lazy);

            $this->addTags($definition, $service);

            $container->setDefinition($id, $definition);
        }

        // resolve arguments after all services are added, so we know all available services here
        foreach ($services as $id => $service) {
            $definition = $container->getDefinition($id);

            $definition->setArguments($this->getConstructorArguments($service, $container));
        }
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
     * @throws \InvalidArgumentException
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
                    throw new \InvalidArgumentException(
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

        $arguments = [];

        foreach ($constructor->getParameters() as $idx => $param) {
            if (isset($service->inject[$param->name])) {
                $arguments[$idx] = $this->convertValueToArgument(
                    $service->inject[$param->name],
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
    private function convertValueToArgument($value, ReflectionParameter $param, ContainerBuilder $container)
    {
        if ($value instanceof InjectableInterface) {
            return $value->getArgument($param, $container);
        }

        if (strpos($value, '%') === 0) {
            return $value;
        }

        if (strpos($value, '@') === 0) {
            return new Reference(substr($value, 1));
        }

        return $value;
    }
}
