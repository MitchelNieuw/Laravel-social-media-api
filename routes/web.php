<?php

Auth::routes();
Route::get('/', static function () {
    return view('welcome');
});

Route::prefix('user')->group(static function () {
    Route::prefix('{userTag}')->group(static function () {
        Route::get('', 'ProfileController@show');
        Route::patch('', 'FollowController@follow');
        Route::patch('unfollow', 'FollowController@unFollow');
        Route::patch('ban', 'BanController@ban');
        Route::patch('unban', 'BanController@unBan');
    });
});

Route::middleware('auth')->group(static function () {
    Route::get('dashboard', 'DashboardController@index');
    Route::get('profile', 'ProfileController@index');
    Route::prefix('message')->group(static function () {
        Route::post('store', 'MessageController@store');
        Route::prefix('{id}')->group(static function () {
            Route::delete('delete', 'MessageController@delete');
        });
    });
});
