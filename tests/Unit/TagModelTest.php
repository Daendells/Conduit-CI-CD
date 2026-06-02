<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Tag;
use Tests\TestCase;

class TagModelTest extends TestCase
{
    public function test_favorite_tags_returns_sorted_results_by_article_count(): void
    {
        $tagA = Tag::factory()->create(['name' => 'tag-a']);
        $tagB = Tag::factory()->create(['name' => 'tag-b']);

        $firstArticle = Article::factory()->create();
        $secondArticle = Article::factory()->create();

        $firstArticle->attachTags([$tagA->name, $tagB->name]);
        $secondArticle->attachTags([$tagA->name]);

        $results = iterator_to_array(Tag::favoriteTags(2));

        $this->assertSame('tag-a', data_get($results[0], 'name'));
        $this->assertSame('tag-b', data_get($results[1], 'name'));
    }
}
