<?php

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use AppendIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;

class FileLoader
{
    /**
     * @param string[] $dirs
     * @param string   $pattern
     *
     * @return SplFileInfo[]|RegexIterator
     */
    public function getPhpFilesOfDirs(array $dirs, $pattern)
    {
        $iterator = new AppendIterator();

        foreach ($dirs as $dir) {
            $iterator->append(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)));
        }

        return new RegexIterator($iterator, $pattern);
    }
}
