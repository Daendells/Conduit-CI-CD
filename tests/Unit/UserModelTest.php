<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_toggle_follow_user_updates_relationship(): void
    {
        $currentUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->assertFalse($currentUser->following($targetUser));

        $this->assertTrue($currentUser->toggleFollowUser($targetUser));
        $this->assertTrue($currentUser->fresh()->following($targetUser));
        $this->assertTrue($targetUser->fresh()->followedBy($currentUser));

        $this->assertFalse($currentUser->toggleFollowUser($targetUser));
        $this->assertFalse($currentUser->fresh()->following($targetUser));
    }

    public function test_is_self_attribute_returns_true_for_logged_in_user(): void
    {
        $user = User::factory()->create();

        auth()->login($user);

        $this->assertTrue($user->isSelf);
    }

    public function test_is_self_attribute_returns_false_when_not_logged_in(): void
    {
        $user = User::factory()->create();

        // Not authenticated
        $this->assertFalse($user->isSelf);
    }

    public function test_is_self_attribute_returns_false_for_different_user(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        auth()->login($userA);

        $this->assertFalse($userB->isSelf);
    }

    public function test_get_image_attribute_returns_default_when_null(): void
    {
        $user = User::factory()->create(['image' => null]);

        $this->assertEquals(
            'https://static.vecteezy.com/system/resources/thumbnails/009/292/244/small/default-avatar-icon-of-social-media-user-vector.jpg',
            $user->image
        );
    }

    public function test_get_image_attribute_returns_set_value(): void
    {
        $imageUrl = 'https://example.com/my-avatar.png';
        $user = User::factory()->create(['image' => $imageUrl]);

        $this->assertEquals($imageUrl, $user->image);
    }
}
