<?php

namespace TestCase\BasicServiceAnnotation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(public=true)
 */
class TestService
{
    /**
     * @var TestRepo
     */
    private $repo;

    public function __construct(TestRepo $repo)
    {
        $this->repo = $repo;
    }

    public function get()
    {
        return $this->repo->fetch();
    }
}
