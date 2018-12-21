#!/usr/bin/php
<?php
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once __DIR__.'/vendor/autoload.php';

try {
    $containerBuilder = new ContainerBuilder();
    $loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__));
    $loader->load('config/services.yml', 'yaml');
    /** @var \Curriculum\Renderer $renderer */
    $renderer = $containerBuilder->get('curriculum.renderer');
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}

$renderer->render();
$renderer->renderAndSave();
