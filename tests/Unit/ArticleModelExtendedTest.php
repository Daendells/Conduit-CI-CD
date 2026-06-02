<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

/**
 * Additional unit tests for Article model to cover missing lines.
 * Covers: scopeFavoritedByUser, scopeOfAuthorsFollowedByUser, attachTags, getRouteKeyName
 */
class ArticleModelExtendedTest extends TestCase
{
    public function test_scope_favorited_by_user_filters_correctly(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $article->toggleUserFavorite($user);

        $results = Article::favoritedByUser($user->username)->get();

        $this->assertTrue($results->contains('id', $article->id));
    }

    public function test_scope_favorited_by_user_excludes_non_favorited(): void
    {
        $user         = User::factory()->create();
        $otherArticle = Article::factory()->create();

        // otherArticle is NOT favorited by $user
        $results = Article::favoritedByUser($user->username)->get();

        $this->assertFalse($results->contains('id', $otherArticle->id));
    }

    public function test_scope_of_authors_followed_by_user(): void
    {
        $follower = User::factory()->create();
        $author   = User::factory()->create();
        $article  = Article::factory()->create(['user_id' => $author->id]);

        $follower->toggleFollowUser($author);

        $results = Article::ofAuthorsFollowedByUser($follower)->get();

        $this->assertTrue($results->contains('id', $article->id));
    }

    public function test_scope_of_authors_followed_by_user_excludes_non_followed(): void
    {
        $follower      = User::factory()->create();
        $stranger      = User::factory()->create();
        $otherArticle  = Article::factory()->create(['user_id' => $stranger->id]);

        // follower does NOT follow stranger
        $results = Article::ofAuthorsFollowedByUser($follower)->get();

        $this->assertFalse($results->contains('id', $otherArticle->id));
    }

    public function test_attach_tags_creates_and_links_tags(): void
    {
        $article = Article::factory()->create();

        $article->attachTags(['php', 'laravel', 'testing']);

        $tagNames = $article->fresh()->tags->pluck('name')->toArray();

        $this->assertContains('php', $tagNames);
        $this->assertContains('laravel', $tagNames);
        $this->assertContains('testing', $tagNames);
    }

    public function test_attach_tags_reuses_existing_tags(): void
    {
        $article = Article::factory()->create();

        // Attach once
        $article->attachTags(['php']);
        // Attach again — should not create duplicate
        $article->attachTags(['php']);

        $tagNames = $article->fresh()->tags->pluck('name')->toArray();
        $phpCount = array_count_values($tagNames)['php'] ?? 0;

        $this->assertEquals(1, $phpCount);
    }

    public function test_get_route_key_name_returns_slug(): void
    {
        $article = new Article();
        $this->assertEquals('slug', $article->getRouteKeyName());
    }

    public function test_article_belongs_to_user(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $article->user);
        $this->assertEquals($user->id, $article->user->id);
    }

    public function test_article_has_many_comments(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $article->comments()->create([
            'user_id' => $user->id,
            'body'    => 'A comment',
        ]);

        $this->assertCount(1, $article->fresh()->comments);
    }

    public function test_article_favorited_users_relationship(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $article->toggleUserFavorite($user);

        $this->assertCount(1, $article->fresh()->favoritedUsers);
    }
}
