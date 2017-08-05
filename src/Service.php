<?php

namespace Symfony\Component\DependencyInjection\Annotation;

use ReflectionClass;
use Symfony\Component\DependencyInjection\Annotation\Inject\MethodAnnotationInterface;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Service
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $inject = [];

    /**
     * @var bool
     */
    public $public;

    /**
     * @var bool
     */
    public $lazy;

    /**
     * @var array
     */
    public $tags = [];

    /**
     * @var ReflectionClass
     */
    private $class;

    /**
     * @var array
     */
    private $methodAnnotations = [];

    /**
     * @param ReflectionClass $class
     */
    public function setClass(ReflectionClass $class)
    {
        $this->class = $class;
    }

    /**
     * @return ReflectionClass
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param MethodAnnotationInterface[][] $annotations
     */
    public function setMethodAnnotations(array $annotations)
    {
        $this->methodAnnotations = $annotations;
    }

    /**
     * @return MethodAnnotationInterface[][]
     */
    public function getAllMethodAnnotations()
    {
        return $this->methodAnnotations;
    }

    /**
     * @param string $methodName
     *
     * @return MethodAnnotationInterface[][]
     */
    public function getMethodAnnotations($methodName)
    {
        if (!isset($this->methodAnnotations[$methodName])) {
            return [];
        }

        return $this->methodAnnotations[$methodName];
    }
}
