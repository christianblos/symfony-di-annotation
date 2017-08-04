<?php
/** @var ContainerInterface $container */

use Example\InjectServicesByInterface\TestService;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/dumped_container.php';
$container = new DumpedServiceContainer();

/** @var TestService $testService */
$testService = $container->get(TestService::class);

if ($testService->get() === 'A,B') {
    echo 'yes';
}
