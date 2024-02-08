<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadLinksRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads/%d/link';

    public function create($client, $preparedData, $amoLeadID): Response|array
    {
        $uri = sprintf(self::$URI, $amoLeadID);
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $request = new Request('POST', $uri, $headers, json_encode($preparedData, JSON_THROW_ON_ERROR));
            return self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getFile());
            Log::warning($ex->getLine());
            return [];
        }
    }

    /**
     * @param $client
     * @param $preparedData
     * @param $amoLeadID
     * @return array|void
     * Description: works on updating
     */
    public function update($client, $preparedData, $amoLeadID)  : Response|array
    {
        $uri = sprintf(self::$URI, $amoLeadID);
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $request = new Request(
                'POST', $uri, $headers,
                json_encode($preparedData, JSON_THROW_ON_ERROR)
            );
            return self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getCode());
            Log::warning($ex->getLine());
            return [];
        }
    }
}
