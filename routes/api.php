<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/search', [ProfileController::class, 'search']);

Route::post('/check-username', function (Request $request) {
    $validator = Validator::make($request->all(), [
        'username' => [
            function ($attribute, $value, $fail) {
                if ($value[0] !== '@') {
                    $fail('USERNAME_MUST_START_WITH_AT');
                }
            },
            'required',
            'unique:users,username',
        ],
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors();
        if($errors->has('username')){
            if($errors->first('username') === 'USERNAME_MUST_START_WITH_AT'){
                return response()->json(['usernameError' => 'El nombre de usuario debe comenzar con @.']);
            } else {
                return response()->json(['usernameError' => 'El nombre de usuario ya está en uso.']);
            }
        }
    }

    return response()->json(['usernameError' => '']);
});
