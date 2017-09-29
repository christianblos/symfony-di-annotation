<?php

namespace Symfony\Component\DependencyInjection\Annotation\Modifier;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Annotation should implement this interface to modify the Service annotation object before
 * it's added to the container.
 */
interface ModifyServiceAnnotationInterface
{
    /**
     * Is called before the Service is added to the container.
     * You should not modify the container here. It doesn't contain all services yet!
     *
     * @param string           $serviceId  The ID that will be used to register the service to the container
     * @param Service          $service    The Service annotation you can modify here
     * @param string           $methodName The method on which this annotation is added to
     * @param ContainerBuilder $container  The current container. Use it to get some config params for instance
     *
     * @return Service
     */
    public function modifyService($serviceId, Service $service, $methodName, ContainerBuilder $container);
}
