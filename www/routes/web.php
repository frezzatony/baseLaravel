<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'    =>  [App\Http\Middleware\SetLayout::class]
], function () {

    Route::group([
        'as'    =>  'password.',
    ], function () {
        Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'create'])->name('request');
        Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('email');
        Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\NewPasswordController::class, 'create'])->name('reset');
        Route::post('/reset-password', [App\Http\Controllers\Auth\NewPasswordController::class, 'store'])->name('store');
    });

    Route::group([
        'as'    =>  'public.',
    ], function () {
        Route::group([
            'as'            =>  'auth.',
        ], function () {
            Route::get('/auth', [App\Http\Controllers\Auth\LoginController::class, 'show'])->name('show');
            Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'show']);
            Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->middleware(App\Http\Middleware\Validate\ValidateAuth::class)->name('login');
            Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
        });
    });

    Route::group([
        'as'            =>  'system.',
    ], function () {

        Route::group([
            'middleware'    =>  ['auth', App\Http\Middleware\SetAuthenticatedLayout::class],
        ], function () {

            Route::get('/system', [App\Http\Controllers\System\SystemController::class, 'index'])->name('main');

            Route::group([
                'as'    =>  'notifications.',
            ], function () {
                Route::get('/system/notifications', [App\Http\Controllers\System\Notifications\NotificationsController::class, 'index'])->name('index');
                Route::get('/system/notifications/view/{id}', [App\Http\Controllers\System\Notifications\NotificationsController::class, 'view'])->name('view');
                Route::delete('/system/notifications/destroy', [App\Http\Controllers\System\Notifications\NotificationsController::class, 'destroy'])->name('destroy');
            });

            Route::group([
                'as'    =>  'users.',
            ], function () {
                Route::get('/system/users', [App\Http\Controllers\System\UsersController::class, 'index'])->middleware('can:system_routine,"gerenciar_usuarios"')->name('index');
                Route::get('/system/users/create', [App\Http\Controllers\System\UsersController::class, 'create'])->middleware('can:system_routine,"gerenciar_usuarios"')->name('create');
                Route::post('/system/users/store', [App\Http\Controllers\System\UsersController::class, 'store'])->middleware('can:system_routine,"gerenciar_usuarios"')->name('store');
                Route::get('/system/users/edit/{id}', [App\Http\Controllers\System\UsersController::class, 'edit'])->middleware('can:system_routine,"gerenciar_usuarios"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/users/update', [App\Http\Controllers\System\UsersController::class, 'update'])->middleware('can:system_routine,"gerenciar_usuarios"')->name('update');
                Route::get('/system/users/detail', [App\Http\Controllers\System\UsersController::class, 'detail'])->name("detail");
                Route::patch('/system/users/modify', [App\Http\Controllers\System\UsersController::class, 'modify'])->name('modify');
                Route::patch('/system/users/update-config', [App\Http\Controllers\System\UsersController::class, 'updateConfig'])->name('updateConfig');
                Route::delete('/system/users/destroy', [App\Http\Controllers\System\UsersController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_usuarios"')->name('destroy');
            });

            Route::group([
                'as'    =>  'profiles.',
            ], function () {
                Route::get('/system/profiles', [App\Http\Controllers\System\ProfilesController::class, 'index'])->middleware('can:system_routine,"gerenciar_perfis"')->name('index');
                Route::get('/system/profiles/create', [App\Http\Controllers\System\ProfilesController::class, 'create'])->middleware('can:system_routine,"gerenciar_perfis"')->name('create');
                Route::post('/system/profiles/store', [App\Http\Controllers\System\ProfilesController::class, 'store'])->middleware('can:system_routine,"gerenciar_perfis"')->name('store');
                Route::get('/system/profiles/edit/{id}', [App\Http\Controllers\System\ProfilesController::class, 'edit'])->middleware('can:system_routine,"gerenciar_perfis"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/profiles/update', [App\Http\Controllers\System\ProfilesController::class, 'update'])->middleware('can:system_routine,"gerenciar_perfis"')->name('update');
                Route::delete('/system/profiles/destroy', [App\Http\Controllers\System\ProfilesController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_perfis"')->name('destroy');
            });

            Route::group([
                'as'    =>  'modules.',
            ], function () {
                Route::get('/system/modules', [App\Http\Controllers\System\ModulesController::class, 'index'])->middleware('can:system_routine,"gerenciar_modulos"')->name('index');
                Route::get('/system/modules/change/{moduleSlug}', [App\Http\Controllers\System\ModulesController::class, 'change'])->name('change');
                Route::get('/system/modules/create', [App\Http\Controllers\System\ModulesController::class, 'create'])->middleware('can:system_routine,"gerenciar_modulos"')->name('create');
                Route::post('/system/modules/store', [App\Http\Controllers\System\ModulesController::class, 'store'])->middleware('can:system_routine,"gerenciar_modulos"')->name('store');
                Route::get('/system/modules/edit/{id}', [App\Http\Controllers\System\ModulesController::class, 'edit'])->middleware('can:system_routine,"gerenciar_modulos"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/modules/update', [App\Http\Controllers\System\ModulesController::class, 'update'])->middleware('can:system_routine,"gerenciar_modulos"')->name('update');
                Route::delete('/system/modules/destroy', [App\Http\Controllers\System\ModulesController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_modulos"')->name('destroy');
            });

            Route::group([
                'as'    =>  'routines.',
            ], function () {
                Route::get('/system/routines', [App\Http\Controllers\System\RoutinesController::class, 'index'])->middleware('can:system_routine,"gerenciar_rotinas"')->name('index');
                Route::get('/system/routines/create', [App\Http\Controllers\System\RoutinesController::class, 'create'])->middleware('can:system_routine,"gerenciar_rotinas"')->name('create');
                Route::post('/system/routines/store', [App\Http\Controllers\System\RoutinesController::class, 'store'])->middleware('can:system_routine,"gerenciar_rotinas"')->name('store');
                Route::get('/system/routines/edit/{id}', [App\Http\Controllers\System\RoutinesController::class, 'edit'])->middleware('can:system_routine,"gerenciar_rotinas"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/routines/update', [App\Http\Controllers\System\RoutinesController::class, 'update'])->middleware('can:system_routine,"gerenciar_rotinas"')->name('update');
                Route::delete('/system/routines/destroy', [App\Http\Controllers\System\RoutinesController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_rotinas"')->name('destroy');
            });

            Route::group([
                'as'    =>  'attendanceunits.',
            ], function () {
                Route::get('/system/attendanceunits', [App\Http\Controllers\System\AttendanceUnitsController::class, 'index'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('index');
                Route::get('/system/attendanceunits/create', [App\Http\Controllers\System\AttendanceUnitsController::class, 'create'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('create');
                Route::post('/system/attendanceunits/store', [App\Http\Controllers\System\AttendanceUnitsController::class, 'store'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('store');
                Route::get('/system/attendanceunits/edit/{id}', [App\Http\Controllers\System\AttendanceUnitsController::class, 'edit'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/attendanceunits/update', [App\Http\Controllers\System\AttendanceUnitsController::class, 'update'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('update');
                Route::delete('/system/attendanceunits/destroy', [App\Http\Controllers\System\AttendanceUnitsController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('destroy');
                Route::any('/system/attendanceunits/attachments', [App\Http\Controllers\System\AttendanceUnitsController::class, 'attachments'])->middleware('can:system_routine,"gerenciar_unidades_atendimento"')->name('attachments');
            });

            Route::group([
                'as'    =>  'holidays.',
            ], function () {
                Route::get('/system/holidays', [App\Http\Controllers\System\HolidaysController::class, 'index'])->middleware('can:system_routine,"gerenciar_feriados"')->name('index');
                Route::get('/system/holidays/create', [App\Http\Controllers\System\HolidaysController::class, 'create'])->middleware('can:system_routine,"gerenciar_feriados"')->name('create');
                Route::post('/system/holidays/store', [App\Http\Controllers\System\HolidaysController::class, 'store'])->middleware('can:system_routine,"gerenciar_feriados"')->name('store');
                Route::get('/system/holidays/edit/{id}', [App\Http\Controllers\System\HolidaysController::class, 'edit'])->middleware('can:system_routine,"gerenciar_feriados"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/holidays/update', [App\Http\Controllers\System\HolidaysController::class, 'update'])->middleware('can:system_routine,"gerenciar_feriados"')->name('update');
                Route::delete('/system/holidays/destroy', [App\Http\Controllers\System\HolidaysController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_feriados"')->name('destroy');
            });

            Route::group([
                'as'    =>  'persons.',
            ], function () {
                Route::get('/system/persons', [App\Http\Controllers\System\Persons\PersonsController::class, 'index'])->middleware('can:system_routine,"visualizar_pessoas"')->name('index');
                Route::any('/system/persons/attachments', [App\Http\Controllers\System\Persons\PersonsController::class, 'attachments'])->middleware('can:system_routine,"gerenciar_pessoas"')->name('attachments');
                Route::get('/system/persons/natural/create', [App\Http\Controllers\System\Persons\NaturalController::class, 'create'])->middleware('can:system_routine,"gerenciar_pessoas"')->name('create');
                Route::post('/system/persons/natural/store', [App\Http\Controllers\System\Persons\NaturalController::class, 'store'])->middleware('can:system_routine,"gerenciar_pessoas"')->name('store');
                Route::get('/system/persons/natural/edit/{id}', [App\Http\Controllers\System\Persons\NaturalController::class, 'edit'])->middleware('can:system_routine,"gerenciar_pessoas"')->where('id', '[0-9]+')->name('edit');
                Route::put('/system/persons/natural/update', [App\Http\Controllers\System\Persons\NaturalController::class, 'update'])->middleware('can:system_routine,"gerenciar_pessoas"')->name('update');
                Route::delete('/system/persons/destroy', [App\Http\Controllers\System\Persons\PersonsController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_pessoas"')->name('destroy');
            });

            Route::group([
                'as'    =>  'queues.',
            ], function () {
                Route::get('/system/queues', [App\Http\Controllers\System\Queues\QueuesController::class, 'index'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('index');
                Route::delete('/system/queues/destroy', [App\Http\Controllers\System\Queues\QueuesController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('destroy');

                Route::group([
                    'as'    =>  'firstcometotem.',
                ], function () {
                    Route::get('/system/queues/firstcometotem/create', [App\Http\Controllers\System\Queues\FirstComeTotemController::class, 'create'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('create');
                    Route::post('/system/queues/firstcometotem/store', [App\Http\Controllers\System\Queues\FirstComeTotemController::class, 'store'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('store');
                    Route::get('/system/queues/firstcometotem/edit/{id}', [App\Http\Controllers\System\Queues\FirstComeTotemController::class, 'edit'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->where('id', '[0-9]+')->name('edit');
                    Route::put('/system/queues/firstcometotem/update', [App\Http\Controllers\System\Queues\FirstComeTotemController::class, 'update'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('update');
                });

                Route::group([
                    'as'    =>  'firstcomemanual.',
                ], function () {
                    Route::get('/system/queues/firstcomemanual/create', [App\Http\Controllers\System\Queues\FirstComeManualController::class, 'create'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('create');
                    Route::post('/system/queues/firstcomemanual/store', [App\Http\Controllers\System\Queues\FirstComeManualController::class, 'store'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('store');
                    Route::get('/system/queues/firstcomemanual/edit/{id}', [App\Http\Controllers\System\Queues\FirstComeManualController::class, 'edit'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->where('id', '[0-9]+')->name('edit');
                    Route::put('/system/queues/firstcomemanual/update', [App\Http\Controllers\System\Queues\FirstComeManualController::class, 'update'])->middleware('can:system_routine,"gerenciar_filas_atendimento"')->name('update');
                });
            });

            Route::group([
                'as'    =>  'custumerservices.',
            ], function () {
                Route::group([
                    'as'    =>  'presential.',
                ], function () {
                    Route::get('/system/customerservices/presential', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'index'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('index');
                    Route::get('/system/customerservices/presential/create', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'create'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('create');
                    Route::get('/system/customerservices/presential/edit/{idQueue}/{idCustomerService}', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'edit'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('edit');
                    Route::put('/system/customerservices/presential/update', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'update'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('update');
                    Route::put('/system/customerservices/presential/update-rate', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'updateRate'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('update_rate');
                    Route::any('/system/customerservices/presential/attachments', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'attachments'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('attachments');
                    Route::get('/system/customerservices/presential/fetchProvideTicketScreen', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'fetchProvideTicketScreen'])->middleware('can:system_routine,"triagem_atendimentos"')->name('fetchProvideTicketScreen');
                    Route::get('/system/customerservices/presential/fetchBookTicket', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'fetchBookTicket'])->middleware('can:system_routine,"triagem_atendimentos"')->name('fetchBookTicket');
                    Route::get('/system/customerservices/presential/fetchBookAndCallDispenserTicket', [App\Http\Controllers\System\CustomerServices\PresentialController::class, 'fetchBookAndCallDispenserTicket'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('fetchBookAndCallDispenserTicket');

                    Route::group([
                        'as'    =>  'rate.',
                    ], function () {
                        Route::get('/system/customerservices/presential/rate/edit/{idQueue}/{idCustomerService}', [App\Http\Controllers\System\CustomerServices\RatePresentialController::class, 'edit'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('edit');
                    });

                    Route::group([
                        'as'    =>  'my_matters.',
                    ], function () {
                        Route::get('/system/customerservices/mymatters/edit/{idQueue}', [App\Http\Controllers\System\CustomerServices\MyMattersController::class, 'edit'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('edit');
                        Route::put('/system/customerservices/mymatters/update', [App\Http\Controllers\System\CustomerServices\MyMattersController::class, 'update'])->middleware('can:system_routine,"efetuar_atendimento_presencial"')->name('update');
                    });
                });
            });

            Route::group([
                'as'    =>  'forum.',
            ], function () {
                Route::get('/system/forum', [App\Http\Controllers\System\Forum\ForumController::class, 'index'])->middleware('can:system_routine,"gerenciar_feriados"')->name('index');
            });

            Route::group([
                'as'    =>  'messages.',
            ], function () {
                Route::group([
                    'as'    =>  'contacts.',
                ], function () {
                    Route::get('/system/messages/contacts', [App\Http\Controllers\System\Messages\ContactsController::class, 'index'])->middleware('can:system_routine,"visualizar_contatos_notificacoes"')->name('index');
                    Route::get('/system/messages/contacts/create', [App\Http\Controllers\System\Messages\ContactsController::class, 'create'])->middleware('can:system_routine,"gerenciar_contatos_notificacoes"')->name('create');
                    Route::post('/system/messages/contacts/store', [App\Http\Controllers\System\Messages\ContactsController::class, 'store'])->middleware('can:system_routine,"gerenciar_contatos_notificacoes"')->name('store');
                    Route::get('/system/messages/contacts/edit/{id}', [App\Http\Controllers\System\Messages\ContactsController::class, 'edit'])->middleware('can:system_routine,"gerenciar_contatos_notificacoes"')->where('id', '[0-9]+')->name('edit');
                    Route::put('/system/messages/contacts/update', [App\Http\Controllers\System\Messages\ContactsController::class, 'update'])->middleware('can:system_routine,"gerenciar_contatos_notificacoes"')->name('update');
                    Route::delete('/system/messages/contacts/destroy', [App\Http\Controllers\System\Messages\ContactsController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_contatos_notificacoes"')->name('destroy');
                });

                Route::group([
                    'as'    =>  'categories.',
                ], function () {
                    Route::get('/system/messages/categories', [App\Http\Controllers\System\Messages\CategoriesController::class, 'index'])->middleware('can:system_routine,"visualizar_categorias_notificacoes"')->name('index');
                    Route::get('/system/messages/categories/create', [App\Http\Controllers\System\Messages\CategoriesController::class, 'create'])->middleware('can:system_routine,"gerenciar_categorias_notificacoes"')->name('create');
                    Route::post('/system/messages/categories/store', [App\Http\Controllers\System\Messages\CategoriesController::class, 'store'])->middleware('can:system_routine,"gerenciar_categorias_notificacoes"')->name('store');
                    Route::get('/system/messages/categories/edit/{id}', [App\Http\Controllers\System\Messages\CategoriesController::class, 'edit'])->middleware('can:system_routine,"gerenciar_categorias_notificacoes"')->where('id', '[0-9]+')->name('edit');
                    Route::put('/system/messages/categories/update', [App\Http\Controllers\System\Messages\CategoriesController::class, 'update'])->middleware('can:system_routine,"gerenciar_categorias_notificacoes"')->name('update');
                    Route::delete('/system/messages/categories/destroy', [App\Http\Controllers\System\Messages\CategoriesController::class, 'destroy'])->middleware('can:system_routine,"gerenciar_categorias_notificacoes"')->name('destroy');
                });
            });
        });
    });
});

Route::group([
    'as'    =>  'totem'
], function () {
    Route::get('/system/totem/{keyTotem}', [App\Http\Controllers\System\TotemController::class, 'index'])->name('index');
});
