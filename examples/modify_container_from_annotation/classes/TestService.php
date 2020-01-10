<?php

namespace Example\ModifyContainerFromAnnotation;

use Example\ModifyContainerFromAnnotation\Annotation\ListenTo;
use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(public=true, id="myService")
 */
class TestService
{
    /**
     * @ListenTo("someEvent")
     */
    public function doSomethingWhenEventHappens(): void
    {

    }
}
