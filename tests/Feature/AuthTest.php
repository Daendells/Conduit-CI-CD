<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_sign_up_page_is_accessible(): void
    {
        $response = $this->get('/sign-up');

        $response->assertStatus(200);
        $response->assertViewIs('sign-up.index');
    }

    public function test_user_can_register(): void
    {
        $email = 'tester+' . uniqid() . '@example.com';
        $username = 'tester-' . uniqid();

        $response = $this->post('/sign-up', [
            'username' => $username,
            'email' => $email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'username' => $username,
        ]);
    }

    public function test_sign_in_page_is_accessible(): void
    {
        $response = $this->get('/sign-in');

        $response->assertStatus(200);
        $response->assertViewIs('sign-in.index');
    }

    public function test_user_can_login_with_correct_credentials(): void
    {
        $password = 'secret123';

        $user = User::factory()->create([
            'password' => bcrypt($password),
        ]);

        $response = $this->post('/sign-in', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_sign_in_returns_error(): void
    {
        $response = $this->from('/sign-in')->post('/sign-in', [
            'email' => 'missing@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertRedirect('/sign-in');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
