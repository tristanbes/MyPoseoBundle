<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('my_poseo');

        $rootNode
            ->children()
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('key')
                            ->isRequired()
                            ->info('The service is not free. You must provide an API Key which can be found on : http://account.myposeo.com/account/configuration/api')
                        ->end()
                        ->arrayNode('type')
                            ->prototype('array')
                            ->requiresAtLeastOneElement()
                            ->floatNode('version')
                                ->isRequired()
                                ->defaultValue('1.1')
                                ->info('The API version you want to use')
                            ->end()
                            ->scalarNode('base_url')
                                ->isRequired()
                                ->defaultValue('http://api.myposeo.com:80/{version}/m/api/')
                                ->info('The API version you want to use')
                            ->end()
                            ->booleanNode('cache')->defaultTrue()->end()
                            ->scalarNode('cache_ttl')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
