<?php

use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReactionsController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\UserFollowStatusController;

Route::prefix('v1')->group(static function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class ,'register']);
    Route::middleware('jwtToken')->group(static function () {
        Route::post('search', [SearchController::class, 'search']);
        Route::prefix('user')->group(static function () {
            Route::get('dashboard', [DashboardController::class, 'index']);
            Route::prefix('messages')->group(static function () {
                Route::get('', [MessageController::class, 'list']);
                Route::post('store', [MessageController::class, 'store']);
                Route::prefix('{id}')->group(static function () {
                    Route::delete('delete', [MessageController::class, 'delete']);
                    Route::prefix('reaction')->group(static function () {
                        Route::post('store', [ReactionsController::class, 'store']);
                        Route::prefix('{reactionId}')->group(static function () {
                            Route::delete('delete', [ReactionsController::class, 'delete']);
                        });
                    });
                });
            });
            Route::prefix('notifications')->group(static function () {
                Route::get('', [NotificationController::class, 'list']);
                Route::prefix('{id}')->group(static function () {
                    Route::delete('delete', [NotificationController::class, 'delete']);
                });
            });
            Route::prefix('{tag}')->group(static function () {
                Route::get('followers', [ProfileController::class, 'getFollowers']);
                Route::get('following', [ProfileController::class, 'getFollowing']);
                Route::get('', [ProfileController::class, 'show']);
                Route::patch('follow', [UserFollowStatusController::class, 'follow']);
                Route::patch('unfollow', [UserFollowStatusController::class, 'unFollow']);
                Route::patch('ban', [UserFollowStatusController::class, 'ban']);
                Route::patch('unban', [UserFollowStatusController::class, 'unBan']);
                Route::patch('notifications-on', [UserFollowStatusController::class, 'notificationsOn']);
                Route::patch('notifications-off', [UserFollowStatusController::class, 'notificationsOff']);
            });
        });
    });
});