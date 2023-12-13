<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\RequestController;
use App\Models\AmoCrmLead;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class LeadRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads';

    public static function create($client, $preparedData, $leadRaw) : void{
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode([$preparedData]));
        self::handleErrors($client, $request, false, $leadRaw);
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
        return self::handleErrors($client, $request, true);
    }

    public static function get($client, $query){
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI.$query, $headers);
        return self::handleErrors($client, $request, true);
    }

    public static function handleSuccess($output, $leadRaw) : void{
        if ($output){
            $result = json_decode($output->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
            if ($result && $result['_embedded']){
                $leadRaw->amoLeadID = $result['_embedded']['leads'][0]['id'];
            }
        }
    }
}
