<?php

namespace Erik\AdminManagerImplementation\Tests\Feature;

use Erik\AdminManagerImplementation\Tests\UserlandAdminToolTest;
use Illuminate\Support\Facades\Route;

class UserlandServiceProviderTest extends UserlandAdminToolTest
{
    public function test_routes_are_registered()
    {
        $this->assertTrue(Route::has('userland.index'), 'Expected route does not exist');
    }

    //TODO: test_routes_have_userland_prefix

    public function test_routes_have_admin_prefix()
    {
        $this->assertContains(config('admin.url_prefix'), route('userland.index'));
    }
}
