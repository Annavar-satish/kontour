<?php

namespace Kontenta\Kontour\Tests;

use Kontenta\Kontour\Tests\Feature\Fakes\UserlandServiceProvider;

abstract class UserlandAdminToolTest extends IntegrationTest
{
    protected $package_providers = [UserlandServiceProvider::class];

    protected function getPackageProviders($app)
    {
        return array_merge($this->package_providers, parent::getPackageProviders($app));
    }

}
