<?php

namespace Example\OwnInjectImplementation;

use Example\OwnInjectImplementation\Annotation\Inject;
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

    /**
     * @var string
     */
    private $param;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @Inject({
     *     "param"="%some_param%",
     *     "finder"="@Example\OwnInjectImplementation\FinderB",
     * })
     */
    public function __construct(TestRepo $repo, $param, FinderInterface $finder)
    {
        $this->repo   = $repo;
        $this->param  = $param;
        $this->finder = $finder;
    }

    public function get()
    {
        return implode(',', [
            $this->repo->fetch(),
            $this->param,
            $this->finder->find(),
        ]);
    }
}
