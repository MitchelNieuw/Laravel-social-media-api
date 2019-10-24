<?php

Route::prefix('v1')->group(static function () {
    Route::namespace('Api')->group(static function () {
        Route::post('login', 'AuthenticationController@login');
        Route::post('register', 'AuthenticationController@register');
        Route::middleware('jwtToken')->group(static function () {
            Route::prefix('user')->group(static function () {
                Route::prefix('messages')->group(static function () {
                    Route::get('', 'MessageController@list');
                    Route::post('store', 'MessageController@store');
                    Route::delete('{id}/delete', 'MessageController@delete');
                });
                Route::prefix('notifications')->group(static function () {
                    Route::get('', 'NotificationController@list');
                    Route::delete('{id}/delete', 'NotificationController@delete');
                });
            });
        });
    });
});