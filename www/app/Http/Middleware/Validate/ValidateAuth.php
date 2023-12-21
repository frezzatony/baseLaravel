<?php

namespace App\Http\Middleware\Validate;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ValidateAuth
{

    public function handle(Request $request, Closure $next): Response
    {
        $validator = Validator::make($request->all(), [
            'username'  => 'required' . (($request->username != '111.111.111-11') ? '|cpf' : ''),
            'password'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('public.auth.show')
                ->withErrors($validator)
                ->withInput();
        }

        return $next($request);
    }
}
