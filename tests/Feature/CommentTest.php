<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $post = Post::factory()->create();

        $response = $this->post("/posts/{$post->id}/comments", [
            'content' => '測試評論內容',
        ]);

        $response->assertRedirect("/posts/{$post->id}");
        $this->assertDatabaseHas('comments', [
            'content' => '測試評論內容',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_user_can_delete_own_comment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->delete("/comments/{$comment->id}");

        $response->assertRedirect("/posts/{$post->id}");
        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_comment()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $this->actingAs($user1);
        
        $post = Post::factory()->create();
        $comment = Comment::factory()->create([
            'user_id' => $user2->id,
            'post_id' => $post->id,
        ]);

        $response = $this->delete("/comments/{$comment->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_guest_cannot_create_comment()
    {
        $post = Post::factory()->create();

        $response = $this->post("/posts/{$post->id}/comments", [
            'content' => '測試評論內容',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', [
            'content' => '測試評論內容',
        ]);
    }
} 