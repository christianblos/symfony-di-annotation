<?php

namespace Example\InjectServicesMappedTo;

use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\Tag\MapTo;

/**
 * @Service(
 *     tags={@MapTo("commandHandler", keyConst="COMMAND_NAME")}
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
