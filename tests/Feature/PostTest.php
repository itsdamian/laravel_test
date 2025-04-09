<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/posts', [
            'title' => '測試文章標題',
            'content' => '測試文章內容',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/posts');
        $this->assertDatabaseHas('posts', [
            'title' => '測試文章標題',
            'content' => '測試文章內容',
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_user_can_update_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $newCategory = Category::factory()->create();

        $response = $this->put("/posts/{$post->id}", [
            'title' => '更新後的標題',
            'content' => '更新後的內容',
            'category_id' => $newCategory->id,
        ]);

        $response->assertRedirect('/posts/'.$post->id);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => '更新後的標題',
            'content' => '更新後的內容',
            'category_id' => $newCategory->id,
        ]);
    }

    public function test_user_can_delete_post()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $this->actingAs($user);
        
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->delete("/posts/{$post->id}");

        $response->assertRedirect('/posts');
        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_guest_cannot_create_post()
    {
        $category = Category::factory()->create();

        $response = $this->post('/posts', [
            'title' => '測試文章標題',
            'content' => '測試文章內容',
            'category_id' => $category->id,
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('posts', [
            'title' => '測試文章標題',
        ]);
    }
} 