<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // 移除auth中间件，使首页对所有用户可见
    }

    /**
     * 显示网站首页
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::where('published', true)
                    ->orderBy('created_at', 'desc')
                    ->paginate(6);
        
        $categories = Category::all();
        
        return view('home', compact('posts', 'categories'));
    }
}
