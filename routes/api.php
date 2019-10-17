<?php

Route::prefix('v1')->group(static function() {
    Route::middleware('jwtToken')->group(static function() {
        Route::post('user/messages/store', 'Api\MessageController@store');
        Route::get('user/messages', 'Api\MessageController@list');
        Route::delete('message/{id}/delete', 'Api\MessageController@delete');
    });
});