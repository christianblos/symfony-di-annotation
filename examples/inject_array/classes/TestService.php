<?php

namespace Example\InjectArray;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     public=true,
 *     inject={
 *         "finder"={"@finderA", "@finderB"}
 *     }
 * )
 */
class TestService
{
    /**
     * @var FinderInterface[]
     */
    private $finder;

    public function __construct(array $finder)
    {
        $this->finder = $finder;
    }

    public function get()
    {
        $values = [];

        foreach ($this->finder as $finder) {
            $values[] = $finder->find();
        }

        return implode(',', $values);
    }
}
