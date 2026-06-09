<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

/**
 * Feature tests for EditorController (standard, non-HTMX).
 */
class EditorTest extends TestCase
{
    public function test_editor_create_page_requires_authentication(): void
    {
        $response = $this->get('/editor');
        // Unauthenticated users are redirected to login
        $response->assertRedirect(route('login'));
    }

    public function test_editor_create_page_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/editor');
        $response->assertStatus(200);
        $response->assertViewIs('editor.create');
    }

    public function test_editor_edit_page_requires_authentication(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/editor/'.$article->slug);
        $response->assertRedirect(route('login'));
    }

    public function test_editor_edit_page_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/editor/'.$article->slug);
        $response->assertStatus(200);
        $response->assertViewIs('editor.edit');
        $response->assertViewHas('article', $article);
    }
}
