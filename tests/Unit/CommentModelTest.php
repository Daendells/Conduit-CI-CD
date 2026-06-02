<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Tests\TestCase;

/**
 * Unit tests for Comment model relationships.
 */
class CommentModelTest extends TestCase
{
    public function test_comment_belongs_to_article(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
        ]);

        $article->comments()->save($comment);

        $this->assertInstanceOf(Article::class, $comment->fresh()->article);
    }

    public function test_comment_belongs_to_user(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $comment = $article->comments()->create([
            'user_id' => $user->id,
            'body'    => 'A test comment body',
        ]);

        $this->assertInstanceOf(User::class, $comment->fresh()->user);
        $this->assertEquals($user->id, $comment->fresh()->user->id);
    }

    public function test_comment_is_created_with_correct_body(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $comment = $article->comments()->create([
            'user_id' => $user->id,
            'body'    => 'Hello from unit test',
        ]);

        $this->assertEquals('Hello from unit test', $comment->body);
    }
}
