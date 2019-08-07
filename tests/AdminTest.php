<?php

namespace Guysolamour\Admin\Tests;

use Guysolamour\Admin\Facades\Admin;
use Guysolamour\Admin\ServiceProvider;
use Orchestra\Testbench\TestCase;

class AdminTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'admin' => Admin::class,
        ];
    }

    public function testExample()
    {
        $this->assertEquals(1, 1);
    }
}
