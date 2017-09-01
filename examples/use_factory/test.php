<?php
/** @var ContainerInterface $container */

use Example\UseFactory\TestService;
use Example\UseFactory\TestStaticService;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/dumped_container.php';
$container = new DumpedServiceContainer();

/** @var TestStaticService $static */
$static = $container->get(TestStaticService::class);

/** @var TestService $service */
$service = $container->get(TestService::class);

if ($static->get() === 'static' && $service->get() === 'fromConstructor,fromArg') {
    echo 'yes';
}
