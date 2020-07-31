<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Annotation\Compiler;

use AppendIterator;
use FilesystemIterator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\Config\Resource\ResourceInterface;

class FileLoader implements FileLoaderInterface
{
    /**
     * @var string[]
     */
    protected array $srcDirs;
    protected string $filePattern;

    /**
     * @param string[] $srcDirs
     * @param string   $filePattern
     */
    public function __construct(array $srcDirs, string $filePattern = '/\.php$/')
    {
        $this->srcDirs     = $srcDirs;
        $this->filePattern = $filePattern;
    }

    /**
     * Get resources for container builder to properly check if container needs to be updated
     * (only when using config cache).
     *
     * @return ResourceInterface[]
     */
    public function getResources(): array
    {
        $resources = [];

        foreach ($this->srcDirs as $srcDir) {
            try {
                $resources[] = new DirectoryResource($srcDir);
            } catch (InvalidArgumentException $ex) {
                //ignore not existing directories
            }
        }

        return $resources;
    }

    /**
     * @return string[]|RegexIterator
     */
    public function getPhpFiles(): iterable
    {
        $iterator = new AppendIterator();

        foreach ($this->srcDirs as $dir) {
            $iterator->append(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, FilesystemIterator::CURRENT_AS_PATHNAME)
                )
            );
        }

        return new RegexIterator($iterator, $this->filePattern);
    }
}
