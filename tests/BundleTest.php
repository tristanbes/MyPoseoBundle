<?php

declare(strict_types=1);

namespace Tristanbes\MyPoseoBundle\Tests;

use PHPUnit\Framework\TestCase;

class BundleTest extends TestCase
{
    public function testBundle(): void
    {
        $kernel = new MyPoseoBundleTestKernel();
        $kernel->boot();

        $container = $kernel->getContainer();

        static::assertInstanceOf(\Tristanbes\MyPoseoBundle\Api\SearchInterface::class, $container->get('Tristanbes\MyPoseoBundle\Api\SearchInterface'));
        static::assertInstanceOf(\Tristanbes\MyPoseoBundle\Connection\RestClient::class, $container->get('Tristanbes\MyPoseoBundle\Connection\RestClient'));
    }
}
