<?php

namespace App\Http\Controllers\api;

use App\Enums\Messages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = Messages::fromKey(mb_strtoupper($request->get('action')));
        if (empty($messages)) {
            abort(404);
        }

        return response()->json([
            'message'   =>  $messages->value[rand(0, sizeof($messages->value) - 1)],
        ]);
    }
}
