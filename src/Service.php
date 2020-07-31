<?php

namespace Symfony\Component\DependencyInjection\Annotation;

use ReflectionClass;

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
    public array $inject = [];

    /**
     * @var array
     */
    public array $methodCalls = [];

    /**
     * @var bool
     */
    public bool $public = false;

    /**
     * @var bool
     */
    public bool $shared = true;

    /**
     * @var bool
     */
    public bool $lazy = false;

    /**
     * @var array
     */
    public array $tags = [];

    /**
     * @var string
     */
    public $factoryClass;

    /**
     * @var string
     */
    public $factoryMethod;

    /**
     * @var array
     */
    public $factoryArguments;

    private ReflectionClass $class;

    /**
     * @var array
     */
    private array $methodAnnotations = [];

    /**
     * @param ReflectionClass $class
     */
    public function setClass(ReflectionClass $class): void
    {
        $this->class = $class;
    }

    /**
     * @return ReflectionClass
     */
    public function getClass(): ReflectionClass
    {
        return $this->class;
    }

    /**
     * @param array $annotations
     */
    public function setMethodAnnotations(array $annotations): void
    {
        $this->methodAnnotations = $annotations;
    }

    /**
     * @return array
     */
    public function getAllMethodAnnotations(): array
    {
        return $this->methodAnnotations;
    }

    /**
     * @param string $methodName
     *
     * @return array
     */
    public function getMethodAnnotations(string $methodName): array
    {
        return $this->methodAnnotations[$methodName] ?? [];
    }
}
