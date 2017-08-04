<?php

namespace Example\ManualInjects;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     public=true,
 *     inject={
 *         "param"="%some_param%",
 *         "finderA"="@Example\ManualInjects\FinderA",
 *         "finderB"="@Example\ManualInjects\FinderB",
 *     }
 * )
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
    private $finderA;

    /**
     * @var FinderInterface
     */
    private $finderB;

    public function __construct(TestRepo $repo, $param, FinderInterface $finderA, FinderInterface $finderB)
    {
        $this->repo    = $repo;
        $this->param   = $param;
        $this->finderA = $finderA;
        $this->finderB = $finderB;
    }

    public function get()
    {
        return implode(',', [
            $this->repo->fetch(),
            $this->param,
            $this->finderA->find(),
            $this->finderB->find(),
        ]);
    }
}
