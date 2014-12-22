<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MyPoseoExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (isset($config['api']['type']['main'])) {
            $loader->load('services.xml');

            $container->setParameter('my_poseo.api.main.version', $config['api']['type']['main']['version']);
            $container->setParameter('my_poseo.api.main.base_url', $config['api']['type']['main']['base_url']);
            $container->setParameter('my_poseo.api.key', $config['api']['key']);
        }

        if (isset($config['api']['type']['search'])) {
            $loader->load('search.xml');

            $container->setParameter('my_poseo.api.search.version', $config['api']['type']['search']['version']);
            $container->setParameter('my_poseo.api.search.base_url', $config['api']['type']['search']['base_url']);
            $container->setParameter('my_poseo.api.key', $config['api']['key']);
        }
    }
}
