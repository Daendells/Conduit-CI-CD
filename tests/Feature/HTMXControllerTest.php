<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

/**
 * Feature tests for all HTMX controllers.
 * Covers: HTMXHomeController, HTMXArticleController, HTMXEditorController,
 *         HTMXSettingsController, HTMXSignInController, HTMXSignUpController,
 *         HTMXUserController
 */
class HTMXControllerTest extends TestCase
{
    // ─────────────────────────────────────────
    // HTMXHomeController
    // ─────────────────────────────────────────

    public function test_htmx_home_index_is_accessible(): void
    {
        $response = $this->get('/htmx/home');
        $response->assertStatus(200);
    }

    public function test_htmx_home_global_feed_is_accessible(): void
    {
        $response = $this->get('/htmx/home/global-feed');
        $response->assertStatus(200);
    }

    public function test_htmx_home_your_feed_requires_auth(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/htmx/home/your-feed');
        $response->assertStatus(200);
    }

    public function test_htmx_home_tag_feed_is_accessible(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get('/htmx/home/tag-feed/' . $tag->name);
        $response->assertStatus(200);
    }

    public function test_htmx_home_tag_list_is_accessible(): void
    {
        $response = $this->get('/htmx/home/tag-list');
        $response->assertStatus(200);
    }

    public function test_htmx_home_favorite_redirects_guest_to_sign_in(): void
    {
        $article = Article::factory()->create();

        $response = $this->post('/htmx/home/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_home_favorite_toggles_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/htmx/home/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────
    // HTMXArticleController
    // ─────────────────────────────────────────

    public function test_htmx_article_show_is_accessible(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/htmx/articles/' . $article->slug);
        $response->assertStatus(200);
    }

    public function test_htmx_article_show_with_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/htmx/articles/' . $article->slug);
        $response->assertStatus(200);
    }

    public function test_htmx_article_favorite_redirects_guest(): void
    {
        $article = Article::factory()->create();

        $response = $this->post('/htmx/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_article_favorite_works_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/htmx/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
    }

    public function test_htmx_article_follow_redirects_guest(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->post('/htmx/articles/follow-user/' . $targetUser->username);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_article_follow_works_for_authenticated_user(): void
    {
        $currentUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $this->actingAs($currentUser);

        $response = $this->post('/htmx/articles/follow-user/' . $targetUser->username);
        $response->assertStatus(200);
    }

    public function test_htmx_article_comments_are_accessible(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/htmx/articles/' . $article->slug . '/comments');
        $response->assertStatus(200);
    }

    public function test_htmx_article_post_comment_works(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/htmx/articles/' . $article->slug . '/comments', [
            'comment' => 'Test HTMX comment',
        ]);

        $response->assertStatus(200);
    }

    public function test_htmx_article_delete_redirects_guest(): void
    {
        $article = Article::factory()->create();

        $response = $this->delete('/htmx/articles/' . $article->slug);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_article_delete_works_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->delete('/htmx/articles/' . $article->slug);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('articles', ['_id' => $article->id]);
    }

    // ─────────────────────────────────────────
    // HTMXEditorController
    // ─────────────────────────────────────────

    public function test_htmx_editor_create_redirects_guest(): void
    {
        $response = $this->get('/htmx/editor');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_editor_create_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/htmx/editor');
        $response->assertStatus(200);
    }

    public function test_htmx_editor_store_redirects_guest(): void
    {
        $response = $this->post('/htmx/editor', [
            'title' => 'Test Article',
            'content' => 'Test content',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_editor_store_creates_article_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/htmx/editor', [
            'title'   => 'My New Article Title',
            'content' => 'Some article content here.',
            'description' => 'A short description',
            'tags' => null,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', [
            'title'   => 'My New Article Title',
            'user_id' => $user->id,
        ]);
    }

    public function test_htmx_editor_store_with_tags(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tagsJson = json_encode([
            (object)['value' => 'laravel'],
            (object)['value' => 'php'],
        ]);

        $response = $this->post('/htmx/editor', [
            'title'       => 'Article With Tags',
            'content'     => 'Content here.',
            'description' => 'Description',
            'tags'        => $tagsJson,
        ]);

        $response->assertStatus(200);
    }

    public function test_htmx_editor_edit_redirects_guest(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/htmx/editor/' . $article->slug);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_editor_edit_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/htmx/editor/' . $article->slug);
        $response->assertStatus(200);
    }

    public function test_htmx_editor_update_redirects_guest(): void
    {
        $article = Article::factory()->create();

        $response = $this->post('/htmx/editor/' . $article->slug, [
            'title'   => 'Updated Title',
            'content' => 'Updated content',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_editor_update_works_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post('/htmx/editor/' . $article->slug, [
            'title'       => 'Updated Article Title',
            'content'     => 'Updated content.',
            'description' => 'Updated description',
            'tags'        => null,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('articles', ['title' => 'Updated Article Title']);
    }

    public function test_htmx_editor_update_with_tags(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $tagsJson = json_encode([
            (object)['value' => 'updated-tag'],
        ]);

        $response = $this->post('/htmx/editor/' . $article->slug, [
            'title'       => 'Updated With Tags',
            'content'     => 'Content.',
            'description' => 'Desc',
            'tags'        => $tagsJson,
        ]);

        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────
    // HTMXSettingsController
    // ─────────────────────────────────────────

    public function test_htmx_settings_index_redirects_guest(): void
    {
        $response = $this->get('/htmx/settings');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_settings_index_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/htmx/settings');
        $response->assertStatus(200);
    }

    public function test_htmx_settings_update_redirects_guest(): void
    {
        $response = $this->post('/htmx/settings', [
            'name'      => 'Test',
            'email'     => 'test@example.com',
            'bio'       => '',
            'image_url' => '',
            'password'  => '',
        ]);
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_settings_update_works_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/htmx/settings', [
            'name'      => 'Updated Name',
            'email'     => $user->email,
            'bio'       => 'My new bio',
            'image_url' => 'https://example.com/image.jpg',
            'password'  => '',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['name' => 'Updated Name']);
    }

    public function test_htmx_settings_update_with_password_change(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/htmx/settings', [
            'name'      => $user->name,
            'email'     => $user->email,
            'bio'       => '',
            'image_url' => '',
            'password'  => 'newpassword123',
        ]);

        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────
    // HTMXSignInController
    // ─────────────────────────────────────────

    public function test_htmx_sign_in_index_is_accessible(): void
    {
        $response = $this->get('/htmx/sign-in');
        $response->assertStatus(200);
    }

    public function test_htmx_sign_in_with_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret')]);

        $response = $this->post('/htmx/sign-in', [
            'email'    => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_htmx_sign_in_with_invalid_credentials_returns_error(): void
    {
        $response = $this->post('/htmx/sign-in', [
            'email'    => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(200);
        $this->assertGuest();
    }

    public function test_htmx_sign_in_wrong_password_returns_error(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct-password')]);

        $response = $this->post('/htmx/sign-in', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(200);
        $this->assertGuest();
    }

    public function test_htmx_logout_logs_user_out(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/htmx/logout');
        $response->assertStatus(200);
        $this->assertGuest();
    }

    // ─────────────────────────────────────────
    // HTMXSignUpController
    // ─────────────────────────────────────────

    public function test_htmx_sign_up_index_is_accessible(): void
    {
        $response = $this->get('/htmx/sign-up');
        $response->assertStatus(200);
    }

    public function test_htmx_sign_up_creates_user_and_logs_in(): void
    {
        $email    = 'htmxtest+' . uniqid() . '@example.com';
        $username = 'htmxuser-' . uniqid();

        $response = $this->post('/htmx/sign-up', [
            'username' => $username,
            'email'    => $email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email'    => $email,
            'username' => $username,
        ]);
    }

    // ─────────────────────────────────────────
    // HTMXUserController
    // ─────────────────────────────────────────

    public function test_htmx_user_show_is_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/htmx/users/' . $user->username);
        $response->assertStatus(200);
    }

    public function test_htmx_user_show_as_self(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/htmx/users/' . $user->username);
        $response->assertStatus(200);
    }

    public function test_htmx_user_show_with_authenticated_user_following(): void
    {
        $currentUser = User::factory()->create();
        $targetUser  = User::factory()->create();
        $currentUser->toggleFollowUser($targetUser);

        $this->actingAs($currentUser);

        $response = $this->get('/htmx/users/' . $targetUser->username);
        $response->assertStatus(200);
    }

    public function test_htmx_user_articles_are_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/htmx/users/' . $user->username . '/articles');
        $response->assertStatus(200);
    }

    public function test_htmx_user_favorite_articles_are_accessible(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/htmx/users/' . $user->username . '/favorites');
        $response->assertStatus(200);
    }

    public function test_htmx_user_follow_redirects_guest(): void
    {
        $targetUser = User::factory()->create();

        $response = $this->post('/htmx/users/' . $targetUser->username . '/follow');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_user_follow_works_for_authenticated_user(): void
    {
        $currentUser = User::factory()->create();
        $targetUser  = User::factory()->create();

        $this->actingAs($currentUser);

        $response = $this->post('/htmx/users/' . $targetUser->username . '/follow');
        $response->assertStatus(200);
    }

    public function test_htmx_user_favorite_article_redirects_guest(): void
    {
        $article = Article::factory()->create();

        $response = $this->post('/htmx/users/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
        $this->assertStringContainsString('/htmx/sign-in', $response->getContent());
    }

    public function test_htmx_user_favorite_article_works_for_authenticated_user(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        // Without HTTP_REFERER header (the isDeleteItem branch is skipped)
        $response = $this->post('/htmx/users/articles/' . $article->slug . '/favorite');
        $response->assertStatus(200);
    }

    public function test_htmx_user_favorite_article_on_own_profile(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Include HTTP_REFERER with the username to trigger the isDeleteItem branch
        $response = $this->withHeaders([
            'HTTP_REFERER' => 'http://localhost/users/' . $user->username,
        ])->post('/htmx/users/articles/' . $article->slug . '/favorite');

        $response->assertStatus(200);
    }
}
