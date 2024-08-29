<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\ChatroomController;
use App\Http\Controllers\Api\NoteController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/users', function (Request $request) {
    try {
        $users = User::all();
        return response()->json([
            'status' => 200,
            'users' => $users
        ]);
    } catch (\Throwable $th) {
        throw $th;
    }
})->middleware('auth:api');

Route::group(['namespace' => 'Api', 'prefix' => 'v1'], function () {
    Route::post('register', [AuthenticationController::class, 'createUser'])->middleware('guest:api');
    Route::post('login', [AuthenticationController::class, 'store'])->middleware('guest:api');
    Route::post('logout', [AuthenticationController::class, 'destroy'])->middleware('auth:api');    
    Route::get('refresher', [AuthenticationController::class, 'refresher'])->middleware('auth:api');    
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'v1'], function(){
    // Route::resource('note', App\Http\Controllers\Api\NoteController::class);
    Route::get('note',[NoteController::class,'index']);
    Route::post('note/store',[NoteController::class,'store']);


    // Chatting Section
    Route::get('chats/{chatroom}',[ChatController::class, 'getChat']);
    Route::post('chats/store',[ChatController::class, 'store']);
    
    Route::get('chatroom',[ChatroomController::class, 'index']);
    Route::post('chatroom/store',[ChatroomController::class, 'store']);
});