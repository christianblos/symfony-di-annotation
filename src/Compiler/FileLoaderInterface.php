<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use RegexIterator;
use Symfony\Component\Config\Resource\ResourceInterface;

interface FileLoaderInterface
{

    /**
     * Get resources for container builder to properly check if container needs to be updated
     * (only when using config cache).
     *
     * @return ResourceInterface[]
     */
    public function getResources(): array;

    /**
     * @return string[]|RegexIterator
     */
    public function getPhpFiles(): iterable;
}
