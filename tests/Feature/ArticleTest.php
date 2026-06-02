<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    public function test_article_show_page_is_accessible(): void
    {
        $article = Article::factory()->create();

        $response = $this->get('/articles/' . $article->slug);

        $response->assertStatus(200);
        $response->assertViewIs('articles.detail');
    }

    public function test_authenticated_user_can_favorite_article(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/htmx/articles/' . $article->slug . '/favorite');

        $response->assertStatus(200);
        $this->assertTrue($article->fresh()->favoritedByUser($user));
    }

    public function test_authenticated_user_can_post_comment(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/htmx/articles/' . $article->slug . '/comments', [
            'comment' => 'This is a test comment.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'body' => 'This is a test comment.',
        ]);
    }

    public function test_authenticated_user_can_delete_article(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->delete('/htmx/articles/' . $article->slug);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('articles', [
            'id' => $article->id,
        ]);
    }
}
