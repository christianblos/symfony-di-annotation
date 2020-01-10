<?php

namespace Example\ModifyContainerFromAnnotation;

use Symfony\Component\DependencyInjection\Annotation\Service;

/**
 * @Service(public=true)
 */
class EventDispatcher
{
    private $result = '';

    public function addListener($event, $method): void
    {
        $this->result .= $event . ' -> ' . $method;
    }

    public function get(): string
    {
        return $this->result;
    }
}
