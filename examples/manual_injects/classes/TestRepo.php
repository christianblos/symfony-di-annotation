<?php

namespace Example\ManualInjects;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service()
 */
class TestRepo
{
    public function fetch(): string
    {
        return 'fetch';
    }
}
