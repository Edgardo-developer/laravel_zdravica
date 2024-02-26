<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use JsonException;

class ProductRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/catalogs/12348/elements';

    public function create(Client $client, array $preparedData): array
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $jsonData = json_encode($preparedData, JSON_THROW_ON_ERROR);
            dd($jsonData);
            $request = new Request('POST', self::$URI, $headers, $jsonData);
            return self::validateCreateResponse(self::handleErrors($client, $request));
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return [];
        }
    }

    public function update(Client $client, array $preparedData, $amoProductID): void
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $jsonData  = json_encode([$preparedData], JSON_THROW_ON_ERROR);
            $request = new Request('POST', self::$URI . '/' . $amoProductID, $headers, $jsonData);
            self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return;
        }
    }

    private static function validateCreateResponse($res): array
    {
        if ($res) {
            try {
                $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                if ($result && $result['_embedded']) {
                    $ids = [];
                    foreach ($result['_embedded']['elements'] as $element) {
                        $ids[] = $element['id'];
                    }
                    return $ids;
                }
            } catch (JsonException $ex) {
                Log::warning($ex->getMessage());
                Log::warning($ex->getFile());
                Log::warning($ex->getLine());
            }
        }
        return [];
    }
}
