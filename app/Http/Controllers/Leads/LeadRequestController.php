<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads';

    public static function create($client, $preparedData): string
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $preparedData['status_id'] = 61034286;
        try {
            $request = new Request('POST', self::$URI, $headers, json_encode([$preparedData], JSON_THROW_ON_ERROR));
            $res = self::handleErrors($client, $request, false);
            if ($res) {
                $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                if ($result && $result['_embedded']) {
                    return $result['_embedded']['leads'][0]['id'];
                }
            }
        }catch (\JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getFile());
            Log::warning($ex->getLine());
        }
        return 0;
    }

    /**
     * @param $client
     * @param $preparedData
     * @return array|void
     * Description: works on updating
     */
    public static function update($client, $preparedData)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $leadID = $preparedData['amoLeadID'];
        unset($preparedData['amoLeadID']);
        try {
            $request = new Request(
                'PATCH', self::$URI.'/'.$leadID, $headers,
                json_encode($preparedData, JSON_THROW_ON_ERROR)
            );
            return self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getLine());
            return;
        }
    }

    public static function get($client, $query)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI . $query, $headers);
        return self::handleErrors($client, $request, true);
    }
}
