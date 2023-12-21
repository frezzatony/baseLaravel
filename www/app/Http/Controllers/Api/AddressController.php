<?php

namespace App\Http\Controllers\api;

use App\Enums\States;
use App\Helpers\StringHelper;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\GuzzleException;

class AddressController extends Controller
{
    public function addressByCep($cep)
    {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', "https://viacep.com.br/ws/$cep/json/");
            $address = json_decode($response->getBody(), true);
            if (empty($address['erro'])) {
                $address['estado'] = States::fromKey(StringHelper::upper($address['uf']));
                return response([
                    'status'    =>  'success',
                    'address'   =>  $address,
                ], 200);
            }
        } catch (GuzzleException $e) {
        }
        return response([
            'status'    =>  'error',
            'message'   =>  'O CEP informádo é inválido.',
        ], 400);
    }
}
