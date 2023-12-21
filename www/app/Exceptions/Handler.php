<?php

namespace App\Exceptions;

use App\Services\System\RoutineActionService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, $exception)
    {
        if ($exception instanceof AuthorizationException) {
            if ($request->get('action_slug') && $request->ajax()) {
                $action = RoutineActionService::findAllByFilters(['slug'  =>  $request->get('action_slug')], ['limit' => 1])->first();

                if (empty($action)) {
                    return response()->json([
                        'status'    =>  'error',
                        'message'   =>  'A rotina definida para verificação de permissão não existe.<br><small>Consulte o administrador do sistema. </small>',
                    ], 404);
                }

                return response()->json(
                    array_merge([
                        'status'        =>  'error',
                        'message'       =>  'Usuário sem permissão de acesso.',
                        'permission'    =>  true,
                    ], (array)$action),
                    403
                );
            }
        }
        return parent::render($request, $exception);
    }
}
