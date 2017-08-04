<?php

namespace Example\OwnInjectImplementation\Annotation;

use Symfony\Component\DependencyInjection\Annotation\Inject\MethodAnnotationInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @Annotation
 */
class Inject implements MethodAnnotationInterface
{
    /**
     * @var array
     */
    public $value;

    /**
     * @param Service          $service
     * @param string           $methodName
     * @param ContainerBuilder $container
     *
     * @return Service
     */
    public function modifyService(Service $service, $methodName, ContainerBuilder $container)
    {
        $injects = [$service->inject];

        foreach ($service->getMethodAnnotations($methodName) as $methodAnnotation) {
            if ($methodAnnotation instanceof self) {
                $injects[] = $methodAnnotation->value;
            }
        }

        $service->inject = array_merge(...$injects);

        return $service;
    }
}
