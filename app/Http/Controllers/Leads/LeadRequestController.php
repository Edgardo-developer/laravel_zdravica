<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\RequestController;

class LeadRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads/';
    public static function create($client, $preparedData){

    }

    public static function update($client){

    }

    public static function get($client, $query){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new \GuzzleHttp\Psr7\Request('GET', self::$URI.$query, $headers);
        return self::handleErrors($client, $request);
    }
}
