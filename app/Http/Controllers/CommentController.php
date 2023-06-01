<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
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

        return redirect()->back();
    }

    public function destroy($postId, $commentId)
    {
        $comment = Comment::where('post_id', $postId)->findOrFail($commentId);

        // Puedes agregar una comprobación adicional aquí para asegurarte de que el usuario actual es el autor del comentario.
        // Si no lo es, puedes devolver una respuesta con un código de estado 403 (Prohibido).
        if (auth()->user()->id !== $comment->user_id) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $comment->delete();

        // recarga la página o redirige a la página de inicio
        return redirect()->back();
    }

    public function show($postId)
    {
        $post = Post::find($postId)->load('comments');
        return response()->json($post);
    }
}
