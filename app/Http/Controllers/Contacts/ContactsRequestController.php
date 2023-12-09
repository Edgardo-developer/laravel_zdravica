<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendToAmoCRM;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class ContactsRequestController extends \App\Http\Controllers\RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';

    public static function create($client, $preparedData){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt;
        $request = new Request('POST', self::$URI, $headers, json_encode($preparedData));
        $res = $client->sendAsync($request)->wait();
        self::handleResponseCodes($res->getStatusCode());
        return $res;
    }

    public static function update($client){
        // does not need
    }

    public static function get($client, $query) : array{
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt;
        $request = new Request('GET', self::$URI.$query, $headers);
        $res = self::handleErrors($client, $request);
        self::handleResponseCodes($res->getStatusCode());
        try {
            $result = $res->getBody() !== '' ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
        }catch (\JsonException $exception){
            print_r($exception);
        }
    }
}
