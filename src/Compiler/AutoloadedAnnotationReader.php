<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use function class_exists;

class AutoloadedAnnotationReader extends AnnotationReader
{
    private static bool $autoloaderRegistered = false;

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
