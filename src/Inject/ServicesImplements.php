<?php

namespace Symfony\Component\DependencyInjection\Annotation\Inject;

use Doctrine\Common\Annotations\Annotation;
use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @Annotation
 * @Target({"ANNOTATION"})
 */
class ServicesImplements implements InjectableInterface
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
     */
    public function getArgument(ReflectionParameter $param, ContainerBuilder $container)
    {
        if (!$this->value) {
            return [];
        }

        $references = [];

        foreach ($container->getDefinitions() as $serviceId => $definition) {
            $className = $definition->getClass();
            if ($className === null) {
                continue;
            }

            $class = new ReflectionClass($className);
            if (!$class->implementsInterface($this->value)) {
                continue;
            }

            $references[] = new Reference($serviceId);
        }

        return $references;
    }
}
