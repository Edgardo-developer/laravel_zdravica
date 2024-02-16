<?php

namespace App\Http\Controllers\Bill;


use App\Http\Controllers\RequestController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use JsonException;

class BillRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/catalogs/12352/elements';

    public static function create(Client $client, array $preparedData): int
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $jsonData = json_encode([$preparedData], JSON_THROW_ON_ERROR);
            $request = new Request('POST', self::$URI, $headers, $jsonData);
            return self::validateCreateResponse(self::handleErrors($client, $request));
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getFile());
            Log::warning($ex->getLine());
            return 0;
        }
    }

    private static function validateCreateResponse($res): int
    {
        if ($res) {
            try {
                $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                if ($result && $result['_embedded']) {
                    return $result['_embedded']['elements'][0]['id'];
                }
            } catch (JsonException $ex) {
                Log::warning($ex->getMessage());
                Log::warning($ex->getFile());
                Log::warning($ex->getLine());
            }
        }
        return 0;
    }

    public static function update(Client $client, array $preparedData): array|Response
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try{
            $jsonData = json_encode($preparedData, JSON_THROW_ON_ERROR);
            $request = new Request(
                'PATCH', self::$URI, $headers,
                $jsonData
            );
            Log::info('The JSON data of updating the Bill was: '.$jsonData);
            Log::info('The URL of updating the Bill was: '.self::$URI);
            return self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return [];
        }
    }
}
