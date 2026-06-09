<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Tag;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    public function test_favorite_tags_returns_sorted_results_by_article_count(): void
    {
        // Use unique names so this test is isolated from other tests that
        // create tags (MongoDB has no per-test DB cleanup/rollback)
        $uid = uniqid('tag_');
        $tagA = Tag::factory()->create(['name' => "tag-a-{$uid}"]);
        $tagB = Tag::factory()->create(['name' => "tag-b-{$uid}"]);

        $firstArticle = Article::factory()->create();
        $secondArticle = Article::factory()->create();

        // tag-a gets 2 articles, tag-b gets 1 article
        $firstArticle->attachTags([$tagA->name, $tagB->name]);
        $secondArticle->attachTags([$tagA->name]);

        // Fetch enough results so pollution tags don't push ours out of the list
        $results = iterator_to_array(Tag::favoriteTags(500));
        $tagNames = array_column($results, 'name');

        $tagAPos = array_search($tagA->name, $tagNames);
        $tagBPos = array_search($tagB->name, $tagNames);

        $this->assertNotFalse($tagAPos, "{$tagA->name} not found in favoriteTags result");
        $this->assertNotFalse($tagBPos, "{$tagB->name} not found in favoriteTags result");
        // tag-a (2 articles) must appear before tag-b (1 article)
        $this->assertLessThan($tagBPos, $tagAPos, "{$tagA->name} should rank higher than {$tagB->name}");
    }
}
