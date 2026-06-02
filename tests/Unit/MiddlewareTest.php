<?php

namespace Tests\Unit;

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Unit tests for HTTP Middleware.
 */
class MiddlewareTest extends TestCase
{
    // ─────────────────────────────────────────
    // Authenticate middleware
    // ─────────────────────────────────────────

    public function test_authenticate_redirects_to_login_for_non_json_requests(): void
    {
        $middleware = $this->app->make(Authenticate::class);
        $request    = Request::create('/some-protected-route', 'GET');

        // Use reflection to call the protected redirectTo method
        $reflection = new \ReflectionMethod($middleware, 'redirectTo');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($middleware, $request);

        $this->assertEquals(route('login'), $result);
    }

    public function test_authenticate_returns_null_for_json_requests(): void
    {
        $middleware = $this->app->make(Authenticate::class);
        $request    = Request::create('/api/something', 'GET', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $reflection = new \ReflectionMethod($middleware, 'redirectTo');
        $reflection->setAccessible(true);

        $result = $reflection->invoke($middleware, $request);

        $this->assertNull($result);
    }

    // ─────────────────────────────────────────
    // RedirectIfAuthenticated middleware
    // ─────────────────────────────────────────

    public function test_redirect_if_authenticated_passes_guest_through(): void
    {
        $middleware = new RedirectIfAuthenticated();
        $request    = Request::create('/sign-in', 'GET');

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('OK');
        };

        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }

    public function test_redirect_if_authenticated_redirects_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $middleware = new RedirectIfAuthenticated();
        $request    = Request::create('/sign-in', 'GET');

        $next = function ($req) {
            return response('Should not reach here');
        };

        $response = $middleware->handle($request, $next);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals(
            RouteServiceProvider::HOME,
            $response->headers->get('Location')
        );
    }

    public function test_redirect_if_authenticated_uses_default_guard_when_no_guards_provided(): void
    {
        $middleware = new RedirectIfAuthenticated();
        $request    = Request::create('/sign-in', 'GET');

        $nextCalled = false;
        $next = function ($req) use (&$nextCalled) {
            $nextCalled = true;
            return response('OK');
        };

        // Call with no extra guard arguments (uses default)
        $middleware->handle($request, $next);

        $this->assertTrue($nextCalled);
    }
}
