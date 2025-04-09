<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * 创建一个新的控制器实例
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['show', 'index']);
    }

    /**
     * 显示所有分类列表
     */
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    /**
     * 显示创建分类表单
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * 存储新创建的分类
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        Category::create($validated);

        return redirect()->route('categories.index')
                        ->with('success', '分类创建成功！');
    }

    /**
     * 显示指定的分类及其文章
     */
    public function show(Category $category)
    {
        $posts = $category->posts()->where('published', true)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);
                        
        return view('categories.show', compact('category', 'posts'));
    }

    /**
     * 显示编辑分类表单
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * 更新指定的分类
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('categories.index')
                        ->with('success', '分类更新成功！');
    }

    /**
     * 删除指定的分类
     */
    public function destroy(Category $category)
    {
        // 检查分类下是否有文章
        if ($category->posts()->count() > 0) {
            return redirect()->route('categories.index')
                            ->with('error', '无法删除该分类，因为它下面有文章！');
        }

        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', '分类删除成功！');
    }
}
