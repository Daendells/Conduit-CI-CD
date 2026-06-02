<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;

class ArticleModelTest extends TestCase
{
    public function test_toggle_user_favorite_updates_favorite_status(): void
    {
        $user = User::factory()->create();
        $article = Article::factory()->create();

        $this->assertFalse($article->favoritedByUser($user));

        $this->assertTrue($article->toggleUserFavorite($user));
        $this->assertTrue($article->fresh()->favoritedByUser($user));

        $this->assertFalse($article->toggleUserFavorite($user));
        $this->assertFalse($article->fresh()->favoritedByUser($user));
    }
}
