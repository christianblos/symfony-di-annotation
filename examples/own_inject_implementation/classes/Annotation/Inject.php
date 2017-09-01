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
        $injects = $this->getMethodInjects($service, $methodName);

        if ($methodName === '__construct') {
            $service->inject = array_merge($service->inject, $injects);
        } else {
            $service->methodCalls[] = [$methodName, $injects];
        }

        return $service;
    }

    /**
     * @param Service $service
     * @param string  $methodName
     *
     * @return array
     */
    private function getMethodInjects(Service $service, $methodName)
    {
        $injects = [];

        foreach ($service->getMethodAnnotations($methodName) as $methodAnnotation) {
            if ($methodAnnotation instanceof self) {
                $injects[] = $methodAnnotation->value;
            }
        }

        if ($injects) {
            return array_merge(...$injects);
        }

        return [];
    }
}
