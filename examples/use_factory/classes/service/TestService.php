<?php

namespace Example\UseFactory;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(
 *     public=true,
 *     factoryClass="@myFactory",
 *     factoryMethod="create",
 *     factoryArguments={"%factoryArg%"}
 * )
 */
class TestService
{
    private $fromConstructor;

    private $fromFactory;

    public function __construct($fromConstructor, $fromFactory)
    {
        $this->fromConstructor = $fromConstructor;
        $this->fromFactory     = $fromFactory;
    }

    public function get(): string
    {
        return $this->fromConstructor . ',' . $this->fromFactory;
    }
}
