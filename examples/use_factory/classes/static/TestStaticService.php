<?php

namespace Example\UseFactory;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     public=true,
 *     factoryClass=TestStaticFactory::class,
 *     factoryMethod="create"
 * )
 */
class TestStaticService
{
    private $passedFromFactory;

    public function __construct($passedFromFactory)
    {
        $this->passedFromFactory = $passedFromFactory;
    }

    public function get()
    {
        return $this->passedFromFactory;
    }
}
