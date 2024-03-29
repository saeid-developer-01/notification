<?php

Route::prefix('notification/api/v1')->middleware(config('notification.admin_middlewares'))->group(function () {
    Route::namespace("IICN\\Notification\\Http\\Controllers")->middleware('auth.notification')->group(function () {
        Route::namespace('Notification')->group(function () {
            Route::post('notifications', 'Store');
            Route::get('notifications', 'Index');
        });
    });
});
