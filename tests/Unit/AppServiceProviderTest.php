<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Tests\TestCase;

/**
 * Unit tests for AppServiceProvider.
 */
class AppServiceProviderTest extends TestCase
{
    public function test_boot_sets_https_in_production(): void
    {
        // Temporarily set environment to production
        $this->app['config']->set('app.env', 'production');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        $this->assertEquals('on', $this->app['request']->server->get('HTTPS'));
    }

    public function test_boot_does_not_set_https_outside_production(): void
    {
        $this->app['config']->set('app.env', 'testing');

        $previousValue = $this->app['request']->server->get('HTTPS');

        $provider = new AppServiceProvider($this->app);
        $provider->boot();

        // HTTPS should not be 'on' (or unchanged) in testing env
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
