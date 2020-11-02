<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Tristanbes\MyPoseoBundle\Connection\RestClient;

class DynamicServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (null != $container->getParameter('my_poseo.api.cache_service_id')) {
            $container->getDefinition(RestClient::class)
                ->replaceArgument(3, new Reference($container->getParameter('my_poseo.api.cache_service_id'))
            );
        }

        if (null != $container->getParameter('my_poseo.api.http_client')) {
            $container->getDefinition(RestClient::class)
                ->replaceArgument(2, new Reference($container->getParameter('my_poseo.api.http_client'))
            );
        }
    }
}
