<?php

namespace Example\InjectServicesMappedTo;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\MapTo;

/**
 * @Service(
 *     tags={@MapTo("commandHandler", key="commandA")}
 * )
 */
class HandlerA implements CommandHandlerInterface
{
    public function handle()
    {
        return 'A';
    }
}
