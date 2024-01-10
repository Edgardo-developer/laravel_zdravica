<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class ContactsRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';

    public static function create($client, $preparedData, $contactlead = ''){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode($preparedData));
        return self::handleErrors($client, $request, true);
    }

    public static function update($client, $preparedData = null){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $amoID = $preparedData['amoID'];
        unset($preparedData['amoID']);
        $request = new Request('PATCH', self::$URI.'/'.$amoID, $headers, json_encode($preparedData[0]));
        return self::handleErrors($client, $request, true);
    }

    public static function get($client, $query) : array{
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI.$query, $headers);
        $res = self::handleErrors($client, $request, true);
        if ($res){
            try {
                $result = json_decode($res->getBody(), 'true');
                return $result ?? [];
            }catch (JsonException $exception){
                Log::debug($exception);
            }
        }
        return [];
    }
}
