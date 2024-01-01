<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;

class LeadLinksRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads/%d/links';

    public static function create($client, $preparedData) : string{
        if ($preparedData['amoLeadID']){
            $uri = sprintf(self::$URI, $preparedData['amoLeadID']);
            unset($preparedData['amoLeadID']);
            $RequestExt = self::getRequestExt();
            $headers = $RequestExt['headers'];
            $request = new Request('POST', $uri, $headers, json_encode([$preparedData]));
            return self::handleErrors($client, $request, true);
        }
        return '';
    }

    /**
     * @param $client
     * @param $preparedData
     * @return array|void
     * Description: works on updating
     */
    public static function update($client, $preparedData){
        $uri = sprintf(self::$URI, $preparedData['amoLeadID']);
        unset($preparedData['amoLeadID']);
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('PATCH', $uri, $headers,
        json_encode($preparedData));
        return self::handleErrors($client, $request, true);
    }
}
