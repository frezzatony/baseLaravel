<?php

namespace App\Http\Middleware;

use App\Services\System\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLayout
{
    public function handle(Request $request, Closure $next): Response
    {
        $isAjax = $request->ajax();
        $request->attributes->add([
            '_layout'  =>   $isAjax ? 'blank' : 'master',
        ]);

        return $next($request);
    }
}
