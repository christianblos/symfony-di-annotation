<?php
declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Annotation;

use Symfony\Component\DependencyInjection\ServiceLocator;
use function array_keys;

class ServiceMap extends ServiceLocator
{
    /**
     * @var string[]
     */
    private array $ids;

    /**
     * @param callable[] $factories
     */
    public function __construct(array $factories)
    {
        parent::__construct($factories);

        $this->ids = array_keys($factories);
    }

    /**
     * @return string[]
     */
    public function getIds(): array
    {
        return $this->ids;
    }
}
