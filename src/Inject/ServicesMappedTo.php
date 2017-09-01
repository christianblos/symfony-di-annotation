<?php

namespace Symfony\Component\DependencyInjection\Annotation\Inject;

use Doctrine\Common\Annotations\Annotation;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Annotation\ServiceMap;
use Symfony\Component\DependencyInjection\Annotation\Tag\MapTo;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ServicesMappedTo implements InjectableInterface
{
    /**
     * @var string
     * @Required
     */
    public $value;

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
        $injects  = [];

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

                $injects[] = [
                    'key'       => isset($tag['key']) && $tag['key'] ? $tag['key'] : $className,
                    'serviceId' => $serviceId,
                    'priority'  => isset($tag['priority']) ? $tag['priority'] : 0,
                ];
            }
        }

        usort($injects, [$this, 'sortInjects']);

        foreach ($injects as $inject) {
            $references[$inject['key']] = new ServiceClosureArgument(new Reference($inject['serviceId']));
        }

        return new Definition(ServiceMap::class, [$references]);
    }

    /**
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    private function sortInjects(array $a, array $b)
    {
        if ($a['priority'] == $b['priority']) {
            return 0;
        }

        return $a['priority'] > $b['priority'] ? -1 : 1;
    }
}
