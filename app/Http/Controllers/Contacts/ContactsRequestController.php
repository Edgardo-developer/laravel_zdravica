<?php

namespace App\Http\Controllers\Contacts;


use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class ContactsRequestController extends \App\Http\Controllers\RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';

    public static function create($client, $preparedData){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode($preparedData));
        return self::handleErrors($client, $request);
    }

    public static function update($client, $preparedData = null){
        // does not need
    }

    public static function get($client, $query) : array{
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI.$query, $headers);
        $res = self::handleErrors($client, $request);
        try {
            $result = json_decode($res->getBody(), 'true');
            return $result ?? [];
        }catch (\JsonException $exception){
            Log::debug($exception);
        }
        return [];
    }
}
