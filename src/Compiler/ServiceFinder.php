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
    public function findServiceAnnotations($files): array
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

                    $id            = $annotation->id ?: $refClass->getName();
                    $services[$id] = $annotation;
                }
            }
        }

        return $services;
    }

    /**
     * @param ReflectionClass $refClass
     *
     * @return array
     */
    private function getMethodAnnotations(ReflectionClass $refClass): array
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
