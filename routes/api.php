<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['prefix' => 'v1'], function(){
    // Route::resource('permisi', 'PermisiController', [
    //     'only' => ['create', 'edit']
    // ]);
    Route::post('/permisi/lihat_data', [
        'uses' => 'PermisiController@view'
    ]);
    Route::post('/permisi/neraca', [
        'uses' => 'PermisiController@neraca'
    ]);
});

