<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Annotation\Service;

class ServiceFinder
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param string[]|iterable $files
     *
     * @return Service[] Indexed by serviceId
     */
    public function findServiceAnnotations($files)
    {
        $includedFiles = [];

        foreach ($files as $file) {
            require_once $file;

            $includedFiles[$file] = true;
        }

        $services = [];

        foreach (get_declared_classes() as $className) {
            $refClass  = new ReflectionClass($className);
            $classFile = $refClass->getFileName();

            // only read included files
            if (!isset($includedFiles[$classFile])) {
                continue;
            }

            if (!$refClass->getDocComment()) {
                continue;
            }

            /** @var Service $serviceAnnotation */
            $annotations       = $this->annotationReader->getClassAnnotations($refClass);
            $methodAnnotations = null;

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Service) {
                    $annotation->setClass($refClass);

                    if ($methodAnnotations === null) {
                        $methodAnnotations = $this->getMethodAnnotations($refClass);
                    }
                    $annotation->setMethodAnnotations($methodAnnotations);

                    $id            = $this->getServiceId($annotation);
                    $services[$id] = $annotation;
                }
            }
        }

        return $services;
    }

    /**
     * @param Service $service
     *
     * @return string
     */
    protected function getServiceId(Service $service)
    {
        return $service->id ?: $service->getClass()->getName();
    }

    /**
     * @param ReflectionClass $refClass
     *
     * @return array
     */
    private function getMethodAnnotations(ReflectionClass $refClass)
    {
        $annotations = [];

        foreach ($refClass->getMethods() as $method) {
            if (!$method->getDocComment()) {
                continue;
            }

            $methodAnnotations = $this->annotationReader->getMethodAnnotations($method);
            foreach ($methodAnnotations as $methodAnnotation) {
                $annotations[$method->getName()][] = $methodAnnotation;
            }
        }

        return $annotations;
    }
}
