<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/leads';

    /**
     * @param Client $client
     * @param array $preparedData
     * @param int $status
     * @return int
     */
    public function create(Client $client, array $preparedData, int $status = 61034286): int
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $preparedData['status_id'] = $status;
        try {
            $request = new Request('POST',
                self::$URI, $headers,
                json_encode([$preparedData], JSON_THROW_ON_ERROR));
            $res = self::handleErrors($client, $request);
            if ($res) {
                $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                if ($result && $result['_embedded']) {
                    return (int)$result['_embedded']['leads'][0]['id'];
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
     * @param Client $client
     * @param $preparedData
     * @return array|void
     * Description: works on updating
     */
    public function update(Client $client, $preparedData)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        if (!isset($preparedData['delete'])){
            $leadID = $preparedData['amoLeadID'];
            $uri = self::$URI.'/'.$leadID;
            unset($preparedData['amoLeadID']);
        }else{
            $uri = self::$URI;
            unset($preparedData['delete']);
        }
        try {
            $request = new Request(
                'PATCH', $uri, $headers,
                json_encode($preparedData, JSON_THROW_ON_ERROR)
            );
            return self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getLine());
            return [];
        }
    }

    /**
     * @param Client $client
     * @param $query
     * @return false|mixed
     */
    public static function get(Client $client, $query)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI . $query, $headers);
        return self::handleErrors($client, $request);
    }
}
