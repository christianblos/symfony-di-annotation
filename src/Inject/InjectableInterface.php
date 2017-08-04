<?php

namespace Symfony\Component\DependencyInjection\Annotation\Inject;

use ReflectionParameter;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface InjectableInterface
{
    /**
     * @param ReflectionParameter $param
     * @param ContainerBuilder    $container
     *
     * @return mixed
     */
    public function getArgument(ReflectionParameter $param, ContainerBuilder $container);
}
