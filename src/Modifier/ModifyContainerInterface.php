<?php

namespace Symfony\Component\DependencyInjection\Annotation\Modifier;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Annotation should implement this interface to modify any service in the container.
 */
interface ModifyContainerInterface
{
    /**
     * This method is called after all services are registered.
     * You can modify any service in the container here :)
     *
     * @param string           $serviceId  The ID of the service on which this annotation is added to
     * @param Service          $service    The source Service annotation. Just if you need infos from it
     * @param Definition       $definition The registered service definition of the class on which this annotation is
     *                                     added to.
     * @param string           $methodName The method on which this annotation is added to
     * @param ContainerBuilder $container  The container which you can modify here
     */
    public function modifyContainer(
        $serviceId,
        Service $service,
        Definition $definition,
        $methodName,
        ContainerBuilder $container
    );
}
