<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/* Enlazamos la página 'Home' con el postController */
Route::get('/', [PostController::class, 'index'])->name('post.home');
Route::post('/posts', [PostController::class, 'store'])->name('post.store');
Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('post.destroy');

Route::get('/notifications', function () {
    return Inertia::render('Notifications');
})->name('pages.notifications');

Route::get('/messages', function () {
    return Inertia::render('Messages');
})->name('pages.messages');


Route::get('/logout', function () {
    Auth::logout();
});

Route::post('/post/{post}/like', [ProfileController::class, 'like']);
Route::post('/post/{post}/unlike', [ProfileController::class, 'unlike']);
Route::post('/post/{post}/dislike', [ProfileController::class, 'dislike']);
Route::post('/post/{post}/undislike', [ProfileController::class, 'undislike']);

Route::get('/profile/{userId}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile', [ProfileController::class, 'showProfile'])->name('pages.profile');
Route::post('/profile/update/name', [ProfileController::class, 'updateName'])->name('profile.update.name');
Route::post('/profile/update/biography', [ProfileController::class, 'updateBiography'])->name('profile.update.biography');


Route::get('/register', [HomeController::class, 'register'])->name('register');
Route::get('/login', [HomeController::class, 'login'])->name('login');
require __DIR__.'/auth.php';

Auth::routes();
