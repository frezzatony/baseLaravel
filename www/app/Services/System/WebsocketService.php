<?php

namespace App\Services\System;

use App\Services\CrudService;
use WebSocket\Client;

class WebsocketService extends CrudService
{

    public static function message(string $key, string $message)
    {
        $client = new Client("ws://127.0.0.1:8080");
        $client->text(json_encode([$key => $message]));
    }
}
