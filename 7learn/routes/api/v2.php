<?php

use App\Http\Controllers\API\V2\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('post',[PostController::class,'index']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Use Route:fallback to handle 404 Http Requests
Route::fallback(function (Request $request){
    $uri = str_replace('api/v2', 'api/v1', $request->getRequestUri());
    return redirect($uri);
});
