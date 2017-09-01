<?php

namespace Example\InjectServicesByInterface;

use Symfony\Component\DependencyInjection\Annotation\Inject\ServicesImplements;
use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     public=true,
 *     inject={
 *         "services"=@ServicesImplements("\Example\InjectServicesByInterface\FinderInterface")
 *     }
 * )
 */
class TestService
{
    /**
     * @var FinderInterface[]
     */
    private $services;

    /**
     * @param FinderInterface[] $services
     */
    public function __construct(array $services)
    {
        $this->services = $services;
    }

    public function get()
    {
        $result = [];

        foreach ($this->services as $finder) {
            $result[] = $finder->find();
        }

        return implode(',', $result);
    }
}
