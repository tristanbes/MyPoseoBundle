<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MyPoseoExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['api']['type']['search'])) {
            $loader->load('search.yaml');

            $container->setParameter('my_poseo.api.search.base_url', $config['api']['type']['search']['base_url']);
            $container->setParameter('my_poseo.api.key', $config['api']['key']);
        }

        $container->setParameter('my_poseo.api.search_class', $config['api']['search_class']);
        $container->setParameter('my_poseo.api.cache_service_id', $config['api']['cache_service_id']);
        $container->setParameter('my_poseo.api.http_client', $config['api']['http_client']);
    }
}
