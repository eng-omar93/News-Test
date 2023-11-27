<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::prefix('v1')->group(function () {


    //no auth
    Route::prefix('lookup')->group(function()
    {
        Route::get('/categories', [App\Http\Controllers\API\LookupController::class, 'categories'] );
        Route::get('/sources', [App\Http\Controllers\API\LookupController::class, 'sources'] );
        Route::get('/authors', [App\Http\Controllers\API\LookupController::class, 'authors'] );
    });

    Route::prefix('news')->group(function()
    {
        Route::get('/', [App\Http\Controllers\API\NewsController::class, 'index'] );
        Route::get('/user-preferences', [App\Http\Controllers\API\NewsController::class, 'userPreferences'] );
    });

});

Route::fallback(function () {
    echo json_encode('unknown route');
    return;
});
