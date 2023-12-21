<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'as'            =>  'api.',
], function () {

    Route::get('/app-message/{action?}', [App\Http\Controllers\Api\MessageController::class, 'index'])->name('app-message');


    Route::group([
        'as'            =>  'totem.',
    ], function () {
        Route::get('/system/totem/book', [App\Http\Controllers\System\TotemController::class, 'book'])->name('book');
    });

    Route::group([
        'as'            =>  'system.',
        'middleware'    =>  ['auth:api']
    ], function () {
        Route::get('/system/fetch/items/{serviceName}', [App\Http\Controllers\Api\FetchController::class, 'items'])->name('items');

        Route::group([
            'as'            =>  'notifications.',
        ], function () {
            Route::post('/notifications/resume-unread', [App\Http\Controllers\Api\NotificationController::class, 'fetchResumeUnreadNotifications'])->name('resume-unread');
            Route::post('/notifications/notification', [App\Http\Controllers\Api\NotificationController::class, 'fetchNotification'])->name('notification');
            Route::post('/notifications/mark-all-as-read', [App\Http\Controllers\Api\NotificationController::class, 'fetchMarkAllAsRead'])->name('mark-all-as-read');
        });

        Route::group([
            'as'            =>  'address.',
        ], function () {
            Route::get('/system/address/findbycep/{cep}', [App\Http\Controllers\Api\AddressController::class, 'addressByCep'])->name('by-cep');
        });

        Route::group([
            'as'            =>  'customer_services.',
        ], function () {
            Route::post('/system/queue/callbook', [App\Http\Controllers\Api\System\CustomerServices\CustomerServicesController::class, 'callBook'])->name('call-book');
            Route::post('/system/queue/cancelbook', [App\Http\Controllers\Api\System\CustomerServices\CustomerServicesController::class, 'cancelBook'])->name('cancel-book');
        });
    });
});
