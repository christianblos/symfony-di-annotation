<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use InvalidArgumentException;
use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Annotation\Inject\InjectableInterface;
use Symfony\Component\DependencyInjection\Annotation\Modifier\ModifyContainerInterface;
use Symfony\Component\DependencyInjection\Annotation\Modifier\ModifyServiceAnnotationInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\TagInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use function array_map;
use function is_array;
use function is_string;
use function sprintf;
use function strpos;
use function substr;

class AnnotationPass implements CompilerPassInterface
{
    private FileLoaderInterface $fileLoader;
    private ServiceFinder $serviceFinder;
    private bool $trackDirectoryResources;

    public function __construct(
        FileLoaderInterface $fileLoader,
        ServiceFinder $serviceFinder,
        bool $trackDirectoryResources = true
    ) {
        $this->fileLoader              = $fileLoader;
        $this->serviceFinder           = $serviceFinder;
        $this->trackDirectoryResources = $trackDirectoryResources;
    }

    /**
     * @param string[] $srcDirs
     * @param string   $filePattern
     *
     * @return AnnotationPass
     */
    public static function createDefault(array $srcDirs, string $filePattern = '/\.php$/'): AnnotationPass
    {
        $fileLoader    = new FileLoader($srcDirs, $filePattern);
        $serviceFinder = new ServiceFinder(new AutoloadedAnnotationReader());

        return new self($fileLoader, $serviceFinder);
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     * @throws ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container): void
    {
        if ($this->trackDirectoryResources) {
            foreach ($this->fileLoader->getResources() as $resource) {
                $container->addResource($resource);
            }
        }

        $files    = $this->fileLoader->getPhpFiles();
        $services = $this->serviceFinder->findServiceAnnotations($files);
        unset($files);

        // register all services first so they are known on the further steps
        foreach ($services as $id => $service) {
            $service = $this->modifyServiceByMethodAnnotations($id, $service, $container);

            $container->setDefinition($id, $this->createServiceDefinition($service));

            $className = $service->getClass()->getName();
            if ($className !== $id) {
                $container->setAlias($className, $id);
            }
        }

        foreach ($services as $id => $service) {
            $definition = $container->getDefinition($id);

            // resolve arguments and method calls
            if (!$service->factoryClass) {
                $definition->setArguments($this->getConstructorArguments($service, $container));

                foreach ($service->methodCalls as $methodCall) {
                    $this->addMethodCall($definition, $methodCall, $service, $container);
                }
            }

            $this->modifyContainerByMethodAnnotations($id, $service, $definition, $container);
        }
    }

    /**
     * @param Service $service
     *
     * @return Definition
     * @throws InvalidArgumentException
     */
    protected function createServiceDefinition(Service $service): Definition
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
     * @param string           $serviceId
     * @param Service          $service
     * @param ContainerBuilder $container
     *
     * @return Service
     */
    private function modifyServiceByMethodAnnotations(string $serviceId, Service $service, ContainerBuilder $container): Service
    {
        foreach ($service->getAllMethodAnnotations() as $method => $methodAnnotations) {
            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof ModifyServiceAnnotationInterface) {
                    $service = $methodAnnotation->modifyService($serviceId, $service, $method, $container);
                }
            }
        }

        return $service;
    }

    /**
     * @param string           $serviceId
     * @param Service          $service
     * @param Definition       $definition
     * @param ContainerBuilder $container
     */
    private function modifyContainerByMethodAnnotations(
        string $serviceId,
        Service $service,
        Definition $definition,
        ContainerBuilder $container
    ): void {
        foreach ($service->getAllMethodAnnotations() as $method => $methodAnnotations) {
            foreach ($methodAnnotations as $methodAnnotation) {
                if ($methodAnnotation instanceof ModifyContainerInterface) {
                    $methodAnnotation->modifyContainer($serviceId, $service, $definition, $method, $container);
                }
            }
        }
    }

    /**
     * @param Definition $definition
     * @param Service    $service
     *
     * @throws InvalidArgumentException
     */
    private function addTags(Definition $definition, Service $service): void
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
    private function getConstructorArguments(Service $service, ContainerBuilder $container): array
    {
        if (empty($service->inject)) {
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
     * @param Definition       $definition
     * @param array            $methodCall
     * @param Service          $service
     * @param ContainerBuilder $container
     *
     * @throws InvalidArgumentException
     */
    private function addMethodCall(
        Definition $definition,
        array $methodCall,
        Service $service,
        ContainerBuilder $container
    ): void {
        if (!isset($methodCall[0]) || !is_string($methodCall[0])) {
            throw new InvalidArgumentException(sprintf(
                'invalid methodCall configuration for service %s. Must be [$methodName, $params]',
                $service->id
            ));
        }

        $methodName   = $methodCall[0];
        $methodParams = [];

        if (isset($methodCall[1])) {
            if (!is_array($methodCall[1])) {
                throw new InvalidArgumentException(
                    sprintf('params in methodCall for service %s must be an array', $service->id)
                );
            }
            $methodParams = $methodCall[1];
        }

        $method = $service->getClass()->getMethod($methodName);
        $args   = $this->resolveMethodArguments($method, $methodParams, $container);

        $definition->addMethodCall($methodName, $args);
    }

    /**
     * @param ReflectionMethod $method
     * @param array            $injects
     * @param ContainerBuilder $container
     *
     * @return mixed[] Indexed by param index
     */
    private function resolveMethodArguments(ReflectionMethod $method, array $injects, ContainerBuilder $container): array
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
     * @param string|string[]     $value
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
     * @param string|string[] $value
     *
     * @return mixed
     */
    private function resolveValue($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'resolveValue'], $value);
        }

        if (is_string($value)) {
            if (strpos($value, '%') === 0) {
                return $value;
            }

            if (strpos($value, '@') === 0) {
                return new Reference(substr($value, 1));
            }
        }

        return $value;
    }
}
