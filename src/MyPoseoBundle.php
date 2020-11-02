<?php

declare(strict_types=1);

/**
 * MyPoseo API Bundle
 *
 * @author Tristan Bessoussa <tristan.bessoussa@gmail.com>
 */

namespace Tristanbes\MyPoseoBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tristanbes\MyPoseoBundle\CompilerPass\DynamicServiceCompilerPass;

/**
 * MyPoseo Bundle
 */
class MyPoseoBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DynamicServiceCompilerPass());
    }
}
