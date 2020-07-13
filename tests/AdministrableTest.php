<?php

namespace Guysolamour\Administrable\Tests;

use Guysolamour\Administrable\Facades\Administrable;
use Guysolamour\Administrable\ServiceProvider;
use Orchestra\Testbench\TestCase;

class AdministrableTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'administrable' => Administrable::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
