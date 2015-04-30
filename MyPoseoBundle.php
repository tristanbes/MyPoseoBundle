<?php

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Tristanbes\MyPoseoBundle\CompilerPass\DynamicServiceCacheCompilerPass;

/**
 * MyPoseoBundle class
 */
class MyPoseoBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DynamicServiceCacheCompilerPass());
    }
}
