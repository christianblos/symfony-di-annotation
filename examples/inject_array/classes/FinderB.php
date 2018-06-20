<?php

namespace Example\InjectArray;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(id="finderB")
 */
class FinderB implements FinderInterface
{
    public function find()
    {
        return 'B';
    }
}
