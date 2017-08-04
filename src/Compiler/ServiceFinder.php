<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Annotation\Inject\MethodAnnotationInterface;
use Symfony\Component\DependencyInjection\Annotation\Service;

class ServiceFinder
{
    /**
     * @var FileLoader
     */
    private $fileLoader;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @param FileLoader $fileLoader
     * @param Reader     $annotationReader
     */
    public function __construct(FileLoader $fileLoader, Reader $annotationReader)
    {
        $this->fileLoader       = $fileLoader;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param string[] $dirs
     *
     * @return Service[] Indexed by serviceId
     */
    public function findServiceAnnotations(array $dirs)
    {
        $fileInfos     = $this->fileLoader->getPhpFilesOfDirs($dirs);
        $includedFiles = [];

        foreach ($fileInfos as $file) {
            $realPath = $file->getRealPath();
            require_once $realPath;

            $includedFiles[$realPath] = true;
        }

        $services = [];

        foreach (get_declared_classes() as $className) {
            $refClass = new ReflectionClass($className);

            // only read included files
            if (!isset($includedFiles[$refClass->getFileName()])) {
                continue;
            }

            if (!$refClass->getDocComment()) {
                continue;
            }

            /** @var Service $serviceAnnotation */
            $annotations       = $this->annotationReader->getClassAnnotations($refClass);
            $methodAnnotations = $this->getMethodAnnotations($refClass);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Service) {
                    $annotation->setClass($refClass);
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
     * @return MethodAnnotationInterface[][]
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
                if ($methodAnnotation instanceof MethodAnnotationInterface) {
                    $annotations[$method->getName()][] = $methodAnnotation;
                }
            }
        }

        return $annotations;
    }
}
