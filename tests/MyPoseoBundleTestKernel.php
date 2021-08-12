<?php

namespace Tristanbes\MyPoseoBundle\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Tristanbes\MyPoseoBundle\MyPoseoBundle;

abstract class AbstractMyPoseoBundleTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function __construct()
    {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new MyPoseoBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader): void
    {
        $containerBuilder->loadFromExtension('framework', [
            'secret' => 'my-secret',
            'test'   => true,
            'router' => [
                'utf8' => true,
            ],
        ]);

        $containerBuilder->loadFromExtension('my_poseo', [
            'api' => [
                'key'         => 'my_key',
                'http_client' => null,
                'type'        => [
                    'search' => [
                        'base_url' => 'http://api.myposeo.com/m/apiv2',
                    ],
                ],
            ],
        ]);
    }
}

if (AbstractMyPoseoBundleTestKernel::VERSION_ID >= 50100) { // @phpstan-ignore-line
    class MyPoseoBundleTestKernel extends AbstractMyPoseoBundleTestKernel
    {
        protected function configureRoutes(RoutingConfigurator $routes): void
        {
        }
    }
} else { // @phpstan-ignore-line
    class MyPoseoBundleTestKernel extends AbstractMyPoseoBundleTestKernel
    {
        protected function configureRoutes(RouteCollectionBuilder $routes): void
        {
        }
    }
}
