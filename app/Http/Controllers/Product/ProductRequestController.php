<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class ProductRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/catalogs/12348/elements';

    public static function create($client, $preparedData): array
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $jsonData = json_encode($preparedData, JSON_THROW_ON_ERROR);
            $request = new Request('POST', self::$URI, $headers, $jsonData);
            return self::validateCreateResponse(self::handleErrors($client, $request));
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return false;
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

    public static function update($client, $preparedData): void
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $amoBillID = $preparedData['amoID'];
        unset($preparedData['amoID']);
        try {
            $jsonData  = json_encode([$preparedData], JSON_THROW_ON_ERROR);
            $request = new Request('POST', self::$URI . '/' . $amoBillID, $headers, $jsonData);
            self::handleErrors($client, $request);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return;
        }
    }
}
