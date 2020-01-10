<?php

namespace Example\InjectServicesMappedTo;

use Symfony\Component\DependencyInjection\Annotation\Inject\ServicesMappedTo;
use Symfony\Component\DependencyInjection\Annotation\Service;
use Symfony\Component\DependencyInjection\Annotation\ServiceMap;

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
     * @var ServiceMap
     */
    private $handlers;

    /**
     * @param ServiceMap $handlers
     */
    public function __construct(ServiceMap $handlers)
    {
        $this->handlers = $handlers;
    }

    public function get(): string
    {
        /** @var CommandHandlerInterface $handlerA */
        $handlerA = $this->handlers->get('commandA');

        /** @var CommandHandlerInterface $handlerB */
        $handlerB = $this->handlers->get('commandB');

        return $handlerA->handle() . ',' . $handlerB->handle() . ',' . implode(',', $this->handlers->getIds());
    }
}
