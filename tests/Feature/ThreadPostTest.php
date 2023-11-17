<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ThreadPostTest extends TestCase
{
    use RefreshDatabase;

    // store test for guest
    public function test_guest_cannot_create_reply_post()
    {
        $thread = Thread::factory()->create();

        $response = $this->post(route('posts.store', ['thread' => $thread]), [
            'body' => 'This is reply post body',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('posts', [
            'body' => 'This is reply post body',
        ]);
    }

    // store test for authenticated user
    public function test_authenticated_user_can_create_reply_post()
    {
        $user = User::factory()->create();

        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)
                                        ->post(route('posts.store', ['thread' => $thread]), [
            'body' => 'This is reply post'
        ]);

        $response->assertRedirect("/threads/{$thread->slug}");
        $this->assertDatabaseHas('posts', [
            'body' => 'This is reply post',
            'thread_id' => $thread->id,
        ]);
    }

    // markdown test for creating post
    public function test_it_store_markdown_in_database_and_display_html_to_client()
    {
        $user = User::factory()->create();

        $thread = Thread::factory()->create();

        // create post
        $this->actingAs($user)->post(
            route('posts.store', ['thread' => $thread]
        ), [
            'body' => '**body** of the post',
        ]);

        // store as raw markdown
        $this->assertDatabaseHas('posts', [
            'body' => '**body** of the post'
        ]);

        $response = $this->get(route('forum.show', $thread));

        // render as html in client
        $response->assertInertia(
            fn(Assert $page) => $page->where('posts.data.0.body', "<p><strong>body</strong> of the post</p>\n")
        );
    }

    // validation for creating post
    public function test_it_validates_when_creating_the_reply_post()
    {
        $user = User::factory()->create();

        $thread = Thread::factory()->create();

        $response = $this->actingAs($user)
                                        ->post(
                                            route('posts.store', ['thread' => $thread]), ['body' => '']
                                        );

        $response->assertSessionHasErrors(['body' => 'The text body cannot be empty']);
    }
}
