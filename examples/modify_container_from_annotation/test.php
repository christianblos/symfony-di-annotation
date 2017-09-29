<?php
/** @var ContainerInterface $container */

use Example\ModifyContainerFromAnnotation\EventDispatcher;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/dumped_container.php';
$container = new DumpedServiceContainer();

/** @var EventDispatcher $dispatcher */
$dispatcher = $container->get(EventDispatcher::class);

if ($dispatcher->get() === 'someEvent -> myService::doSomethingWhenEventHappens') {
    echo 'yes';
}
