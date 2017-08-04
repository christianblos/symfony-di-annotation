<?php

namespace Example\InjectServicesByInterface;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service()
 */
class FinderA implements FinderInterface
{
    public function find()
    {
        return 'A';
    }
}
