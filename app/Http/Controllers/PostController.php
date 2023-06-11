<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller{
    public function __construct(){
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $user = auth()->user();
        $posts = Post::with(['user', 'comments'])->orderBy('created_at', 'desc')->paginate(5);
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', $user->id)->where('type', true)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', $user->id)->where('type', false)->exists();
        }
    
        if (request()->expectsJson()) {
            return response()->json($posts);
        }
    
        return Inertia::render('Home', [
            'posts' => $posts,
            'recentUsers' => $recentUsers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $file = null;
        $extension = null;
        $filename = null;
        $path = '';

        if($request->hasFile('file')){
            $file = $request->file('file');
            $request->validate(['file' => 'required|mimes:jpg,jpeg,png,mp4']);
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $extension === 'mp4' ? $path = '/videos/' : $path = '/pics/';
        }

        $post = new Post;

        $post->user_id = auth()->id(); // aquí utilizamos el ID del usuario autenticado
        $post->name = auth()->user()->name;
        $post->handle = auth()->user()->username;
        $post->image = auth()->user()->profile_photo_path;
        $post->post = $request->input('post');
        $post->created_at = now();
        if ($filename){
            $post->file = $path . $filename;
            $post->is_video = $extension === 'mp4' ? true : false;
            $file->move(public_path() . $path, $filename);
        }
        $post->comments = 0;
        $post->likes = 0;

        $post->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        $post = Post::find($id);
    
        if(!is_null($post->file) && file_exists(public_path() . $post->file)){
            unlink(public_path() . $post->file);
        }
    
        $post->delete();
    
        return Inertia::location(route('post.home'));
    }
}
