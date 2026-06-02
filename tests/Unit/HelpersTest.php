<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use App\Support\Helpers;
use Tests\TestCase;

/**
 * Unit tests for Support\Helpers class.
 */
class HelpersTest extends TestCase
{
    // ─────────────────────────────────────────
    // feedNavbarItems()
    // ─────────────────────────────────────────

    public function test_feed_navbar_items_returns_only_global_for_guest(): void
    {
        // No authenticated user
        $items = Helpers::feedNavbarItems();

        $this->assertArrayHasKey('global', $items);
        $this->assertArrayNotHasKey('personal', $items);
    }

    public function test_feed_navbar_items_returns_personal_and_global_for_auth_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $items = Helpers::feedNavbarItems();

        $this->assertArrayHasKey('personal', $items);
        $this->assertArrayHasKey('global', $items);
    }

    public function test_feed_navbar_items_global_has_correct_structure(): void
    {
        $items = Helpers::feedNavbarItems();

        $this->assertArrayHasKey('title', $items['global']);
        $this->assertArrayHasKey('is_active', $items['global']);
        $this->assertArrayHasKey('hx_get_url', $items['global']);
        $this->assertArrayHasKey('hx_push_url', $items['global']);
        $this->assertEquals('Global Feed', $items['global']['title']);
        $this->assertFalse($items['global']['is_active']);
    }

    public function test_feed_navbar_items_personal_has_correct_structure(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $items = Helpers::feedNavbarItems();

        $this->assertArrayHasKey('title', $items['personal']);
        $this->assertEquals('Your Feed', $items['personal']['title']);
        $this->assertFalse($items['personal']['is_active']);
    }

    // ─────────────────────────────────────────
    // userFeedNavbarItems()
    // ─────────────────────────────────────────

    public function test_user_feed_navbar_items_has_correct_structure(): void
    {
        $user  = User::factory()->create();
        $items = Helpers::userFeedNavbarItems($user);

        $this->assertArrayHasKey('personal', $items);
        $this->assertArrayHasKey('favorite', $items);
    }

    public function test_user_feed_navbar_items_personal_is_active_by_default(): void
    {
        $user  = User::factory()->create();
        $items = Helpers::userFeedNavbarItems($user);

        $this->assertTrue($items['personal']['is_active']);
        $this->assertFalse($items['favorite']['is_active']);
    }

    public function test_user_feed_navbar_items_urls_contain_username(): void
    {
        $user  = User::factory()->create();
        $items = Helpers::userFeedNavbarItems($user);

        $this->assertStringContainsString($user->username, $items['personal']['url']);
        $this->assertStringContainsString($user->username, $items['favorite']['url']);
    }

    // ─────────────────────────────────────────
    // redirectToHome()
    // ─────────────────────────────────────────

    public function test_redirect_to_home_returns_response_with_htmx_headers(): void
    {
        $response = Helpers::redirectToHome();

        $this->assertEquals('/', $response->headers->get('HX-Replace-Url'));
        $this->assertEquals('none', $response->headers->get('HX-Reswap'));
    }

    public function test_redirect_to_home_response_contains_htmx_home_url(): void
    {
        $response = Helpers::redirectToHome();

        $this->assertStringContainsString('/htmx/home', $response->getContent());
    }

    // ─────────────────────────────────────────
    // redirectToSignIn()
    // ─────────────────────────────────────────

    public function test_redirect_to_sign_in_returns_response_with_htmx_headers(): void
    {
        $response = Helpers::redirectToSignIn();

        $this->assertEquals('/sign-in', $response->headers->get('HX-Push-Url'));
        $this->assertEquals('none', $response->headers->get('HX-Reswap'));
    }

    public function test_redirect_to_sign_in_response_contains_htmx_sign_in_url(): void
    {
        $response = Helpers::redirectToSignIn();

        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }
}
