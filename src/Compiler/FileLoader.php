<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use AppendIterator;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

class FileLoader
{
    /**
     * @param string[] $dirs
     * @param string   $pattern
     *
     * @return string[]|RegexIterator
     */
    public function getPhpFilesOfDirs(array $dirs, $pattern)
    {
        $iterator = new AppendIterator();

        foreach ($dirs as $dir) {
            $iterator->append(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, FilesystemIterator::CURRENT_AS_PATHNAME)
                )
            );
        }

        return new RegexIterator($iterator, $pattern);
    }
}
