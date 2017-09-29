<?php

namespace Example\ModifyContainerFromAnnotation\Annotation;

use Example\ModifyContainerFromAnnotation\EventDispatcher;
use Symfony\Component\DependencyInjection\Annotation\Modifier\ModifyContainerInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @Annotation
 */
class ListenTo implements ModifyContainerInterface
{
    /**
     * @var string
     */
    public $value;

    /**
     * {@inheritdoc}
     */
    public function modifyContainer(
        $serviceId,
        Service $service,
        Definition $definition,
        $methodName,
        ContainerBuilder $container
    ) {
        $dispatcher = $container->getDefinition(EventDispatcher::class);
        $dispatcher->addMethodCall('addListener', [$this->value, $serviceId . '::' . $methodName]);
    }
}
