<?php

use Symfony\Component\DependencyInjection\Annotation\Compiler\AnnotationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

require_once __DIR__ . '/../../vendor/autoload.php';

$srcDirs = [
    __DIR__ . '/classes',
];

$containerBuilder = new ContainerBuilder();
$containerBuilder->setParameter('some_param', 'param');
$containerBuilder->setParameter('another_param', 'param2');
$containerBuilder->addCompilerPass(AnnotationPass::createDefault($srcDirs));
$containerBuilder->compile();

$dumper      = new PhpDumper($containerBuilder);
$dumpContent = $dumper->dump(['class' => 'DumpedServiceContainer']);

file_put_contents(__DIR__ . '/dumped_container.php', $dumpContent);
