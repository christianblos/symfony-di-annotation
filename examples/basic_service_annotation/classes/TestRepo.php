<?php

namespace Example\BasicServiceAnnotation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service()
 */
class TestRepo
{
    public function fetch()
    {
        return 'foo';
    }
}
