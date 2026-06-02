<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * Feature tests for SettingsController (standard, non-HTMX).
 */
class SettingsTest extends TestCase
{
    public function test_settings_page_requires_authentication(): void
    {
        $response = $this->get('/settings');
        $response->assertRedirect(route('login'));
    }

    public function test_settings_page_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/settings');
        $response->assertStatus(200);
        $response->assertViewIs('settings.index');
    }

    public function test_settings_page_passes_user_to_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/settings');
        $response->assertViewHas('user');
    }
}
