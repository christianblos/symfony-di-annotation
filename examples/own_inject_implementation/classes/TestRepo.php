<?php

namespace Example\OwnInjectImplementation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service()
 */
class TestRepo
{
    public function fetch()
    {
        return 'fetch';
    }
}
