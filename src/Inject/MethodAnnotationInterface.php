<?php

namespace Symfony\Component\DependencyInjection\Annotation\Inject;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface MethodAnnotationInterface
{
    /**
     * @param Service          $service
     * @param string           $methodName
     * @param ContainerBuilder $container
     *
     * @return Service
     */
    public function modifyService(Service $service, $methodName, ContainerBuilder $container);
}
