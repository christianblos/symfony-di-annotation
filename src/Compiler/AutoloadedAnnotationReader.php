<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\SimpleAnnotationReader;

class AutoloadedAnnotationReader extends SimpleAnnotationReader
{
    /**
     * @param string[] $namespaces
     */
    public function __construct(array $namespaces = [])
    {
        parent::__construct();

        AnnotationRegistry::registerLoader(function ($class) {
            return class_exists($class);
        });

        $this->addNamespace('Symfony\Component\DependencyInjection\Annotation');
        $this->addNamespace('Symfony\Component\DependencyInjection\Annotation\Inject');
        $this->addNamespace('Symfony\Component\DependencyInjection\Annotation\Tag');

        foreach ($namespaces as $namespace) {
            $this->addNamespace($namespace);
        }
    }
}
