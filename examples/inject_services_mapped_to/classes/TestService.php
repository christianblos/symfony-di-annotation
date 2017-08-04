<?php

namespace Example\InjectServicesMappedTo;

use Symfony\Component\DependencyInjection\Annotation\Inject\ServicesMappedTo;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @Service(
 *     public=true,
 *    inject={
 *        "handlers"=@ServicesMappedTo("commandHandler")
 *    }
 * )
 */
class TestService
{
    /**
     * @var ServiceLocator
     */
    private $handlers;

    /**
     * @param ServiceLocator $handlers
     */
    public function __construct(ServiceLocator $handlers)
    {
        $this->handlers = $handlers;
    }

    public function get()
    {
        /** @var CommandHandlerInterface $handlerA */
        $handlerA = $this->handlers->get('commandA');

        /** @var CommandHandlerInterface $handlerB */
        $handlerB = $this->handlers->get('commandB');

        return $handlerA->handle() . ',' . $handlerB->handle();
    }
}
