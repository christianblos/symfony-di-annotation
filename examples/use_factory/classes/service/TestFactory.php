<?php

namespace Example\UseFactory;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     id="myFactory",
 *     inject={
 *         "constructorArg"="%constructorArg%"
 *     }
 * )
 */
class TestFactory
{
    private $constructorArg;

    public function __construct($constructorArg)
    {
        $this->constructorArg = $constructorArg;
    }

    public function create($factoryArg)
    {
        return new TestService($this->constructorArg, $factoryArg);
    }
}
