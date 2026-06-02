<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Tests\TestCase;

/**
 * Unit tests for AppServiceProvider.
 */
class AppServiceProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        // Reset HTTPS server variable after each test to avoid state pollution
        $this->app['request']->server->remove('HTTPS');
        parent::tearDown();
    }

    public function test_boot_sets_https_in_production(): void
    {
        $this->app['config']->set('app.env', 'production');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertEquals('on', $this->app['request']->server->get('HTTPS'));
    }

    public function test_boot_does_not_set_https_in_testing_env(): void
    {
        // Ensure we are in testing, not production
        $this->app['config']->set('app.env', 'testing');
        // Ensure HTTPS is not already set from a previous test
        $this->app['request']->server->remove('HTTPS');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertNotEquals('on', $this->app['request']->server->get('HTTPS'));
    }

    public function test_register_does_not_throw(): void
    {
        $provider = new AppServiceProvider($this->app);

        // register() is empty — just ensure it doesn't throw
        $provider->register();
        $this->assertTrue(true);
    }
}
