<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Post;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show($userId): Response
    {
        $user = User::findOrFail($userId);
        $posts = $user->posts; 
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', Auth::id())->where('type', 1)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', Auth::id())->where('type', 0)->exists();
        }
    
        return Inertia::render('Profile/Show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function showProfile(): Response
    {
        $user = Auth::user();
        $posts = $user->posts; 
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', Auth::id())->where('type', 1)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', Auth::id())->where('type', 0)->exists();
        }
    
        return Inertia::render('Profile', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
    
    public function edit(Request $request): Response
    {
        $user = Auth::user();
        $posts = $user->posts; 
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', Auth::id())->where('type', 1)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', Auth::id())->where('type', 0)->exists();
        }
        
        return Inertia::render('Profile/Edit', [
            'auth' => [
                'user' => $user,
            ],
            'posts' => $posts,
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }    
    
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('login');
    }

    /**
     * Permite al usuario interactuar con like solo 1 vez por post o hasta que lo quite
     */
    public function like(Post $post)
    {
        $dislike = $post->likes()->where('user_id', Auth::id())->where('type', 0)->first();
        if ($dislike) {
            $post->likes()->detach($dislike->id);
            $post->increment('likes');
        }
        
        $like = $post->likes()->where('user_id', Auth::id())->where('type', 1)->first();
        if (!$like) {
            Auth::user()->likes()->attach($post->id, ['type' => 1]);
            $post->increment('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    
    public function unlike(Post $post)
    {
        $like = $post->likes()->where('user_id', Auth::id())->where('type', 1)->first();
        
        if ($like) {
            $post->likes()->detach($like->id);
            $post->decrement('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    
    public function dislike(Post $post)
    {
        $like = $post->likes()->where('user_id', Auth::id())->where('type', 1)->first();
        if ($like) {
            $post->likes()->detach($like->id);
            $post->decrement('likes');
        }
        
        $dislike = $post->likes()->where('user_id', Auth::id())->where('type', 0)->first();
        if (!$dislike) {
            Auth::user()->likes()->attach($post->id, ['type' => 0]);
            $post->decrement('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    public function undislike(Post $post)
    {
        $dislike = $post->likes()->where('user_id', Auth::id())->where('type', 0)->first();
        
        if ($dislike) {
            $post->likes()->detach($dislike->id);
            $post->increment('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    
    
}
