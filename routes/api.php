<?php

use App\Http\Controllers\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource("/post" , PostController::class);

Route::post("/post/addComment" , [PostController::class , "addComments"]);
Route::post("/post/deleteComment" , [PostController::class , "deleteComment"]);

Route::get('/csrf-token', function (Request $request) {
    return response()->json([
        'csrfToken' => $request->session()->token(),
    ]);
});

