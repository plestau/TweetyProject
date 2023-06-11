<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CommentController extends Controller{
    public function store(Request $request, $postId){
        $request->validate([
            'comment' => 'required',
            'file' => 'file|mimetypes:video/mp4,image/jpeg,image/png|max:2048'
        ]);

        $file = $request->file('file');
        $isVideo = $file && $file->getMimeType() === 'video/mp4';

        $comment = new Comment;
        $comment->post_id = $postId;
        $comment->user_id = Auth::id();
        $comment->name = Auth::user()->name;
        $comment->username = Auth::user()->username;
        $comment->user_image = Auth::user()->profile_photo_path;
        $comment->comment = $request->comment;
        $comment->file = $file ? Storage::putFile('comments', $file) : null;
        $comment->is_video = $isVideo;
        $comment->save();

        return Inertia::location(route('post.home'));
    }

    public function destroy($postId, $commentId){
        $comment = Comment::where('post_id', $postId)->findOrFail($commentId);

        if (auth()->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comment->delete();
    }

    public function show($postId){
        $post = Post::find($postId)->load('comments');
        return response()->json($post);
    }
}
