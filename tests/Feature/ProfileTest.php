<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    public function test_user_profile_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/users/' . $user->username);

        $response->assertStatus(200);
        $response->assertViewIs('users.show');
    }

    public function test_user_favorites_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/users/' . $user->username . '/favorites');

        $response->assertStatus(200);
        $response->assertViewIs('users.show');
    }

    public function test_authenticated_user_can_follow_and_unfollow_another_user(): void
    {
        $currentUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->actingAs($currentUser);

        $response = $this->post('/htmx/articles/follow-user/' . $targetUser->username);
        $response->assertStatus(200);

        $this->assertTrue($targetUser->fresh()->followers->contains('id', $currentUser->id));

        $response = $this->post('/htmx/articles/follow-user/' . $targetUser->username);
        $response->assertStatus(200);

        $this->assertFalse($targetUser->fresh()->followers->contains('id', $currentUser->id));
    }
}
