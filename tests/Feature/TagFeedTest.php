<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Tests\TestCase;

class TagFeedTest extends TestCase
{
    public function test_tag_feed_page_is_accessible(): void
    {
        $tag = Tag::factory()->create();

        $response = $this->get('/tag-feed/' . $tag->name);

        $response->assertStatus(200);
        $response->assertViewIs('home.index');
    }

    public function test_authenticated_user_can_access_your_feed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/your-feed');

        $response->assertStatus(200);
        $response->assertViewIs('home.index');
    }
}
