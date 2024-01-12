<?php

namespace GrantHolle\Altcha\Tests;

use GrantHolle\Altcha\AltchaServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            AltchaServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        config()->set('altcha.hmac_key', 'test-key');
    }
}
