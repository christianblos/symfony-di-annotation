<?php

namespace Example\InjectArray;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(id="finderA")
 */
class FinderA implements FinderInterface
{
    public function find()
    {
        return 'A';
    }
}
