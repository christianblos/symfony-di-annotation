<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;

class AutoloadedAnnotationReader extends AnnotationReader
{
    /**
     * @var bool
     */
    private static $autoloaderRegistered = false;

    public function __construct()
    {
        $this->registerAutoloader();

        $parser = new DocParser();
        $parser->setIgnoreNotImportedAnnotations(true);

        parent::__construct($parser);
    }

    private function registerAutoloader(): void
    {
        if (self::$autoloaderRegistered) {
            return;
        }

        AnnotationRegistry::registerLoader(function ($class) {
            return class_exists($class);
        });

        self::$autoloaderRegistered = true;
    }
}
