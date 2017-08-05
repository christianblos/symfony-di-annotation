<?php

namespace Symfony\Component\DependencyInjection\Annotation\Inject;

use Doctrine\Common\Annotations\Annotation;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Annotation\Tag\MapTo;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ServicesMappedTo extends Annotation implements InjectableInterface
{
    /**
     * @param ReflectionParameter $param
     * @param ContainerBuilder    $container
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getArgument(ReflectionParameter $param, ContainerBuilder $container)
    {
        if (!$this->value) {
            return [];
        }

        $references = [];

        $services = $container->findTaggedServiceIds(MapTo::TAG_NAME);

        foreach ($services as $serviceId => $tags) {
            $definition = $container->findDefinition($serviceId);

            $className = $definition->getClass();
            if ($className === null) {
                continue;
            }

            foreach ($tags as $tag) {
                if (!isset($tag['mapTo']) || $tag['mapTo'] !== $this->value) {
                    continue;
                }

                $key = $className;

                if (isset($tag['key']) && $tag['key']) {
                    $key = $tag['key'];
                } elseif (isset($tag['keyConst']) && $tag['keyConst']) {
                    $key = $this->getKeyByConstant($className, $tag['keyConst']);
                }

                $references[$key] = new ServiceClosureArgument(new Reference($serviceId));
            }
        }

        return new Definition(ServiceLocator::class, [$references]);
    }

    /**
     * @param string $class
     * @param string $const
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function getKeyByConstant($class, $const)
    {
        $ref = new ReflectionClass($class);

        if (!$ref->hasConstant($const)) {
            throw new \InvalidArgumentException(
                sprintf('class "%s" must have a constant "%s" (defined in ServicesMappedTo tag)', $class, $const)
            );
        }

        return $ref->getConstant($const);
    }
}
