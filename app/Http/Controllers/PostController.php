<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;


class PostController extends Controller
{
    public function __construct()
{
    $this->middleware('auth');
}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        // Obtiene el usuario actual
        $user = auth()->user();
    
        // Obtiene todos los posts con el usuario asociado
        $posts = Post::with('user')->orderBy('created_at', 'desc')->get();
    
        // Para cada post, comprueba si el usuario le ha dado like o dislike
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', $user->id)->where('type', true)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', $user->id)->where('type', false)->exists();
        }
    
        // Envía los posts a la vista
        return Inertia::render('Home', [
            'posts' => $posts,
            'recentUsers' => $recentUsers
        ]);
    }    
     

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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
        $post->comments = rand(5, 500);
        $post->likes = 0;

        $post->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        if(!is_null($post->file) && file_exists(public_path() . $post->file)){
            unlink(public_path() . $post->file);
        }

        $post->delete();

        return redirect()->route('post.home');
    }
}
