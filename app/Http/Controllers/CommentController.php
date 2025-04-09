<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * 创建一个新的控制器实例
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 存储新创建的评论
     */
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|min:2',
        ]);

        $comment = new Comment([
            'content' => $validated['content'],
            'user_id' => Auth::id(),
            'approved' => true,
        ]);

        $post->comments()->save($comment);

        return redirect()->route('posts.show', $post)
                        ->with('success', '评论发表成功！');
    }

    /**
     * 删除指定的评论
     */
    public function destroy(Comment $comment)
    {
        // 确保用户只能删除自己的评论
        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => '您没有权限删除这条评论！'], 403);
        }

        $post = $comment->post;
        $comment->delete();

        return redirect()->route('posts.show', $post)
                        ->with('success', '评论删除成功！');
    }
}
