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
     * @var string
     */
    private $anotherParam;

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

    /**
     * @Inject({
     *     "anotherParam"="%another_param%",
     * })
     */
    public function setterInjection($anotherParam): void
    {
        $this->anotherParam = $anotherParam;
    }

    public function get(): string
    {
        return implode(',', [
            $this->repo->fetch(),
            $this->param,
            $this->finder->find(),
            $this->anotherParam,
        ]);
    }
}
