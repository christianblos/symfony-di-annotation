<?php

namespace Example\InjectServicesMappedTo;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\MapTo;

/**
 * @Service(
 *     tags={@MapTo("commandHandler", key=HandlerB::COMMAND_NAME, priority=9)}
 * )
 */
class HandlerB implements CommandHandlerInterface
{
    const COMMAND_NAME = 'commandB';

    public function handle()
    {
        return 'B';
    }
}
