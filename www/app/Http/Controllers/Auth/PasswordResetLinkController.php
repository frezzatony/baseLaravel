<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordRecoverRequest;
use App\Services\System\UserService;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function create()
    {
        return view('auth.forgot_password');
    }

    public function store(PasswordRecoverRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));
        if ($status != Password::RESET_LINK_SENT) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }
        return view('auth.verify_email', $request->only('email'));
    }

    public function resend(PasswordRecoverRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));
        if ($status != Password::RESET_LINK_SENT) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }
        return view('auth.verify_email', $request->only('email'));
    }
}
