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
}
