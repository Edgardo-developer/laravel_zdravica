<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class LeadRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads';

    public static function create($client, $preparedData) : mixed{
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode([$preparedData]));
        return self::handleErrors($client, $request);
    }

    /**
     * @param $client
     * @param $preparedData
     * @return array|void
     * Description: works on updating
     */
    public static function update($client, $preparedData){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('PATCH', self::$URI, $headers,
        json_encode($preparedData));
        return self::handleErrors($client, $request);
    }

    public static function get($client, $query){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI.$query, $headers);
        return self::handleErrors($client, $request);
    }
}
