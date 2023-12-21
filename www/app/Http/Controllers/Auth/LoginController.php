<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\System\UserService;
use App\Services\System\WebsocketService;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('system.main');
        }
        return view('auth.index');
    }

    public function login(Request $request)
    {
        $user = User::where('login', preg_replace('/[^0-9]/', '', $request->username))->first();
        if (!$user || !Auth::attempt(['login' => $user->login, 'password' => $request->password])) {
            return redirect()->route('public.auth.show');
        }
        $user->api_token = hash('sha256', StringHelper::random(60));
        UserService::update($user, [
            'user'  =>  [
                'api_token' =>  $user->api_token
            ]
        ]);
        UserService::updateAttributes($user);
        Auth::setUser($user);
        $forceSytemRedirect = session()->get('url.force_system');
        session('url.force_system', false);
        return session()->get('url.intended') && !$forceSytemRedirect ? redirect(session()->get('url.intended')) : redirect()->route('system.main');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $session = [
            'error' =>  $request->session()->get('error'),
        ];
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        WebsocketService::message('logout', 'logout');
        return redirect()->route('public.auth.show')->with($session);
    }

    public function passwordRecover()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('system.main');
        }
        return view('auth.password_recovery');
    }

    public function passwordRecoverSendCode()
    {
        if ($this->isLoggedIn()) {
            return redirect()->route('system.main');
        }



        return view('auth.password_recovery_code');
    }

    private function isLoggedIn()
    {
        if (!session()->has('url.intended')) {
            session(['url.intended' => url()->previous()]);
        }

        if (Auth::check()) {
            return true;
        }
        return false;
    }
}
