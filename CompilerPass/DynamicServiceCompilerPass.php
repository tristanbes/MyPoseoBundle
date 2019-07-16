<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DynamicServiceCacheCompilerPass
 */
class DynamicServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($container->getParameter('my_poseo.api.cache_service_id') != null) {
            $container->getDefinition('Tristanbes\MyPoseoBundle\Connection\RestClient')
                ->replaceArgument(3, new Reference($container->getParameter('my_poseo.api.cache_service_id'))
            );
        }

        if ($container->getParameter('my_poseo.api.http_client') != null) {
            $container->getDefinition('Tristanbes\MyPoseoBundle\Connection\RestClient')
                ->replaceArgument(2, new Reference($container->getParameter('my_poseo.api.http_client'))
            );
        }
    }
}
