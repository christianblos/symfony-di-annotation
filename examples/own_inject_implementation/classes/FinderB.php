<?php

namespace Example\OwnInjectImplementation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service()
 */
class FinderB implements FinderInterface
{
    public function find()
    {
        return 'B';
    }
}
