<?php

Route::middleware('apilogger')->group(static function() {
    Route::prefix('v1')->group(static function () {
        Route::namespace('Api')->group(static function () {
            Route::post('login', 'AuthenticationController@login');
            Route::post('register', 'AuthenticationController@register');
            Route::middleware('jwtToken')->group(static function () {
                Route::prefix('user')->group(static function () {
                    Route::get('dashboard', 'DashboardController@index');
                    Route::prefix('messages')->group(static function () {
                        Route::get('', 'MessageController@list');
                        Route::post('store', 'MessageController@store');
                        Route::prefix('{id}')->group(static function () {
                            Route::delete('delete', 'MessageController@delete');
                            Route::prefix('reaction')->group(static function () {
                                Route::post('store', 'ReactionsController@store');
                                Route::delete('{reactionId}/delete', 'ReactionsController@delete');
                            });
                        });
                    });
                    Route::prefix('notifications')->group(static function () {
                        Route::get('', 'NotificationController@list');
                        Route::delete('{id}/delete', 'NotificationController@delete');
                    });
                    Route::prefix('{tag}')->group(static function () {
                        Route::get('followers', 'ProfileController@getFollowers');
                        Route::get('following', 'ProfileController@getFollowing');
                        Route::get('', 'ProfileController@show');
                        Route::patch('follow', 'UserFollowStatusController@follow');
                        Route::patch('unfollow', 'UserFollowStatusController@unFollow');
                        Route::patch('ban', 'UserFollowStatusController@ban');
                        Route::patch('unban', 'UserFollowStatusController@unBan');
                        Route::patch('notifications-on', 'UserFollowStatusController@notificationsOn');
                        Route::patch('notifications-off', 'UserFollowStatusController@notificationsOff');
                    });
                });
            });
        });
    });
});