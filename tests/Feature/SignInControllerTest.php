<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

/**
 * Additional tests for SignInController to cover uncovered branch (line 14).
 * When an already-authenticated user visits /sign-in, they should be redirected.
 */
class SignInControllerTest extends TestCase
{
    public function test_authenticated_user_visiting_sign_in_is_redirected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Authenticated users should be redirected away from sign-in
        $response = $this->get('/sign-in');
        $response->assertRedirect('/');
    }
}
