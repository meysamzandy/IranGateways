<?php

namespace MeysamZnd\IranGateways\Tests;

use MeysamZnd\IranGateways\Facades\IranGateways;
use MeysamZnd\IranGateways\ServiceProvider;
use Orchestra\Testbench\TestCase;

class IranGatewaysTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'iran-gateways' => IranGateways::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
