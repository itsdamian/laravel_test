<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of all posts
     */
    public function index()
    {
        $posts = Post::where('published', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
        
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new post
     */
    public function create()
    {
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable',
            'featured_image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('public/images');
            $validated['featured_image'] = str_replace('public/', 'storage/', $path);
        }

        Post::create($validated);

        return redirect()->route('posts.index')
                        ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified post
     */
    public function show(Post $post)
    {
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified post
     */
    public function edit(Post $post)
    {
        // Ensure user can only edit their own posts
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('posts.index')
                            ->with('error', 'You do not have permission to edit this post!');
        }

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified post
     */
    public function update(Request $request, Post $post)
    {
        // Ensure user can only update their own posts
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('posts.index')
                            ->with('error', 'You do not have permission to update this post!');
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'category_id' => 'required|exists:categories,id',
            'excerpt' => 'nullable',
            'featured_image' => 'nullable|image|max:2048',
            'published' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('public/images');
            $validated['featured_image'] = str_replace('public/', 'storage/', $path);
        }

        $post->update($validated);

        return redirect()->route('posts.show', $post)
                        ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified post
     */
    public function destroy(Post $post)
    {
        // Ensure user can only delete their own posts
        if (Auth::id() !== $post->user_id) {
            return redirect()->route('posts.index')
                            ->with('error', 'You do not have permission to delete this post!');
        }

        $post->delete();

        return redirect()->route('posts.index')
                        ->with('success', 'Post deleted successfully!');
    }
}
