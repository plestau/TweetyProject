<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Post;
use App\Models\User;
use Illuminate\Validation\Rule;

class ProfileController extends Controller{

    /**
     * Display the user's profile form.
     */
    public function show($userId): Response{
        $user = User::findOrFail($userId);
        $posts = Post::with(['user', 'comments'])->orderBy('created_at', 'desc')->paginate(5);
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', Auth::id())->where('type', 1)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', Auth::id())->where('type', 0)->exists();
        }
    
        return Inertia::render('Profile/Show', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }

    public function showProfile(): Response{
        $user = Auth::user();
        $posts = $user->posts()->orderByDesc('created_at')->paginate(5);
        $posts = Post::with(['user', 'comments'])->orderBy('created_at', 'desc')->paginate(5);
    
        foreach ($posts as $post) {
            $post->hasLiked = $post->likes()->where('user_id', Auth::id())->where('type', 1)->exists();
            $post->hasDisliked = $post->likes()->where('user_id', Auth::id())->where('type', 0)->exists();
        }
    
        return Inertia::render('Profile', [
            'user' => $user,
            'posts' => $posts->toArray(5),
        ]);
    }
    
    public function edit(Request $request): Response{
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
    public function update(ProfileUpdateRequest $request): RedirectResponse{
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
    public function destroy(Request $request): RedirectResponse{
        $user = $request->user();

        Auth::logout();

        $user->posts()->delete();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('register');
    }

    /**
     * Permite al usuario interactuar con like solo 1 vez por post o hasta que lo quite
     */
    public function like(Post $post){
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

    public function unlike(Post $post){
        $like = $post->likes()->where('user_id', Auth::id())->where('type', 1)->first();
        
        if ($like) {
            $post->likes()->detach($like->id);
            $post->decrement('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    public function dislike(Post $post){
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
    
    public function undislike(Post $post){
        $dislike = $post->likes()->where('user_id', Auth::id())->where('type', 0)->first();
        
        if ($dislike) {
            $post->likes()->detach($dislike->id);
            $post->increment('likes');
        }
    
        return response()->json(['likes' => $post->likes]);
    }
    
    // funciones para actualizar campos de la tabla Users
    public function updateName(Request $request): RedirectResponse{
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return Redirect::back();
    }

    public function updateBiography(Request $request): RedirectResponse{
        $validator = Validator::make($request->all(), [
            'biography' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $user->biography = $request->biography;
        $user->save();

        return Redirect::back();
    }

    public function updateAvatar(Request $request): RedirectResponse{
        $validator = Validator::make($request->all(), [
            'profile_photo' => ['required', 'image'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $user->profile_photo_path = $request->file('profile_photo')->store('profile-photos', 'public');
        $user->save();

        return Redirect::back();
    }

    public function updateBackground(Request $request): RedirectResponse{
        $validator = Validator::make($request->all(), [
            'background_image' => ['required', 'image'],
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        $user = Auth::user();
        $user->background_image = $request->file('background_image')->store('cover-photos', 'public');
        $user->save();

        return Redirect::back();
    }

    public function changePassword(Request $request){
        $user = Auth::user();

        // Verificar si la contraseña antigua coincide con la contraseña de la cuenta
        if (Hash::check($request->oldPassword, $user->password)) {
            // Actualizar la contraseña con la nueva contraseña
            $user->password = Hash::make($request->newPassword);
            $user->save();

            Auth::logout();

            return Redirect::to('login');
        }

        // Si la contraseña antigua no coincide, retornar un error
        return Redirect::back()->withErrors(['oldPassword' => 'La contraseña antigua no coincide con la contraseña de la cuenta']);
    }

    public function checkPassword(Request $request){
        $user = Auth::user();
    
        if ($user && Hash::check($request->password, $user->password)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteBackground(Request $request): RedirectResponse{
        $user = Auth::user();
        $user->background_image = null;
        $user->save();

        return Redirect::back();
    }

    // Buscador de usuarios
    public function search(Request $request){
        $query = $request->input('query');
        $users = User::where('username', 'LIKE', "%{$query}%")->get();
        return response()->json($users);
    }
}
