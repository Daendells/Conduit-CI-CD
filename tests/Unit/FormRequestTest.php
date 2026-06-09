<?php

namespace Tests\Unit;

use App\Http\Requests\ArticlePostCommentRequest;
use App\Http\Requests\EditorStoreArticleRequest;
use App\Http\Requests\SettingUpdateRequest;
use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tests\TestCase;

/**
 * Unit tests for Form Request classes.
 * Covers: authorize(), rules(), and failedValidation() for all request classes.
 */
class FormRequestTest extends TestCase
{
    // ─────────────────────────────────────────
    // ArticlePostCommentRequest
    // ─────────────────────────────────────────

    public function test_article_post_comment_request_authorize_returns_true(): void
    {
        $request = new ArticlePostCommentRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_article_post_comment_request_rules_require_comment(): void
    {
        $request = new ArticlePostCommentRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('comment', $rules);
        $this->assertEquals('required', $rules['comment']);
    }

    public function test_article_post_comment_request_failed_validation_throws_exception(): void
    {
        $request = new ArticlePostCommentRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['comment' => 'required']));

        $this->expectException(HttpResponseException::class);

        $reflection = new \ReflectionMethod($request, 'failedValidation');
        $reflection->setAccessible(true);
        $reflection->invoke($request, $validator);
    }

    public function test_article_post_comment_request_failed_validation_has_htmx_headers(): void
    {
        $request = new ArticlePostCommentRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['comment' => 'required']));

        try {
            $reflection = new \ReflectionMethod($request, 'failedValidation');
            $reflection->setAccessible(true);
            $reflection->invoke($request, $validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals('innerHTML show:top', $response->headers->get('HX-Reswap'));
            $this->assertEquals('#form-message', $response->headers->get('HX-Retarget'));

            return;
        }

        $this->fail('HttpResponseException was not thrown.');
    }

    // ─────────────────────────────────────────
    // EditorStoreArticleRequest
    // ─────────────────────────────────────────

    public function test_editor_store_article_request_authorize_returns_true(): void
    {
        $request = new EditorStoreArticleRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_editor_store_article_request_rules_structure(): void
    {
        $request = new EditorStoreArticleRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('description', $rules);
        $this->assertArrayHasKey('content', $rules);
        $this->assertArrayHasKey('tags', $rules);
        $this->assertEquals('required', $rules['title']);
        $this->assertEquals('required', $rules['content']);
    }

    public function test_editor_store_article_request_failed_validation_throws_exception(): void
    {
        $request = new EditorStoreArticleRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['title' => 'required']));

        $this->expectException(HttpResponseException::class);

        $reflection = new \ReflectionMethod($request, 'failedValidation');
        $reflection->setAccessible(true);
        $reflection->invoke($request, $validator);
    }

    public function test_editor_store_article_request_failed_validation_has_htmx_headers(): void
    {
        $request = new EditorStoreArticleRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['title' => 'required']));

        try {
            $reflection = new \ReflectionMethod($request, 'failedValidation');
            $reflection->setAccessible(true);
            $reflection->invoke($request, $validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals('innerHTML show:top', $response->headers->get('HX-Reswap'));
            $this->assertEquals('#form-message', $response->headers->get('HX-Retarget'));

            return;
        }

        $this->fail('HttpResponseException was not thrown.');
    }

    // ─────────────────────────────────────────
    // SettingUpdateRequest
    // ─────────────────────────────────────────

    public function test_setting_update_request_authorize_returns_true(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new SettingUpdateRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_setting_update_request_rules_structure(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new SettingUpdateRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('image_url', $rules);
        $this->assertArrayHasKey('name', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('bio', $rules);
        $this->assertArrayHasKey('password', $rules);
    }

    public function test_setting_update_request_failed_validation_throws_exception(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new SettingUpdateRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['name' => 'required']));

        $this->expectException(HttpResponseException::class);

        $reflection = new \ReflectionMethod($request, 'failedValidation');
        $reflection->setAccessible(true);
        $reflection->invoke($request, $validator);
    }

    public function test_setting_update_request_failed_validation_has_htmx_headers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $request = new SettingUpdateRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['name' => 'required']));

        try {
            $reflection = new \ReflectionMethod($request, 'failedValidation');
            $reflection->setAccessible(true);
            $reflection->invoke($request, $validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals('innerHTML show:top', $response->headers->get('HX-Reswap'));
            $this->assertEquals('#settings-form-message', $response->headers->get('HX-Retarget'));

            return;
        }

        $this->fail('HttpResponseException was not thrown.');
    }

    // ─────────────────────────────────────────
    // SignInRequest
    // ─────────────────────────────────────────

    public function test_sign_in_request_authorize_returns_true(): void
    {
        $request = new SignInRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_sign_in_request_rules_structure(): void
    {
        $request = new SignInRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
        $this->assertEquals('required|email', $rules['email']);
        $this->assertEquals('required', $rules['password']);
    }

    public function test_sign_in_request_failed_validation_throws_exception(): void
    {
        $request = new SignInRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['email' => 'required']));

        $this->expectException(HttpResponseException::class);

        $reflection = new \ReflectionMethod($request, 'failedValidation');
        $reflection->setAccessible(true);
        $reflection->invoke($request, $validator);
    }

    public function test_sign_in_request_failed_validation_has_htmx_headers(): void
    {
        $request = new SignInRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['email' => 'required']));

        try {
            $reflection = new \ReflectionMethod($request, 'failedValidation');
            $reflection->setAccessible(true);
            $reflection->invoke($request, $validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals('innerHTML show:top', $response->headers->get('HX-Reswap'));
            $this->assertEquals('#sign-in-form-messages', $response->headers->get('HX-Retarget'));

            return;
        }

        $this->fail('HttpResponseException was not thrown.');
    }

    // ─────────────────────────────────────────
    // SignUpRequest
    // ─────────────────────────────────────────

    public function test_sign_up_request_authorize_returns_true(): void
    {
        $request = new SignUpRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_sign_up_request_rules_structure(): void
    {
        $request = new SignUpRequest();
        $rules = $request->rules();

        $this->assertArrayHasKey('username', $rules);
        $this->assertArrayHasKey('email', $rules);
        $this->assertArrayHasKey('password', $rules);
    }

    public function test_sign_up_request_failed_validation_throws_exception(): void
    {
        $request = new SignUpRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['email' => 'required']));

        $this->expectException(HttpResponseException::class);

        $reflection = new \ReflectionMethod($request, 'failedValidation');
        $reflection->setAccessible(true);
        $reflection->invoke($request, $validator);
    }

    public function test_sign_up_request_failed_validation_has_htmx_headers(): void
    {
        $request = new SignUpRequest();

        $validator = $this->createMock(Validator::class);
        $validator->method('errors')->willReturn(new \Illuminate\Support\MessageBag(['email' => 'required']));

        try {
            $reflection = new \ReflectionMethod($request, 'failedValidation');
            $reflection->setAccessible(true);
            $reflection->invoke($request, $validator);
        } catch (HttpResponseException $e) {
            $response = $e->getResponse();
            $this->assertEquals('innerHTML show:top', $response->headers->get('HX-Reswap'));
            $this->assertEquals('#sign-up-form-messages', $response->headers->get('HX-Retarget'));

            return;
        }

        $this->fail('HttpResponseException was not thrown.');
    }
}
