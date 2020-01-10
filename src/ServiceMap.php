<?php

namespace Symfony\Component\DependencyInjection\Annotation;

use Symfony\Component\DependencyInjection\ServiceLocator;

class ServiceMap extends ServiceLocator
{
    /**
     * @var string[]
     */
    private $ids;

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
