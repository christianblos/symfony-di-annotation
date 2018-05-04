<?php

namespace TestCase\BasicServiceAnnotation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * This class has "repo" as serviceId, but is autowired by it's class name
 *
 * @Service(id="repo")
 */
class TestRepo
{
    public function fetch()
    {
        return 'foo';
    }
}
