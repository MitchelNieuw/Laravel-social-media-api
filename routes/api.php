<?php

use App\Http\Controllers\Api\{
    AuthenticationController,
    DashboardController,
    MessageController,
    NotificationController,
    ProfileController,
    ReactionsController,
    SearchController,
    UserFollowStatusController
};
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(static function () {
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('register', [AuthenticationController::class ,'register']);

    Route::middleware('auth:api')->group(static function () {
        Route::post('logout', [AuthenticationController::class, 'logout']);
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
                Route::get('', [ProfileController::class, 'show']);
                Route::get('followers', [ProfileController::class, 'getFollowers']);
                Route::get('following', [ProfileController::class, 'getFollowing']);
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
