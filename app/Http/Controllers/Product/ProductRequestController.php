<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class ProductRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/catalogs/12352/elements';

    public static function create($client, $preparedData): array
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode([$preparedData]));
        return self::validateCreateResponse(self::handleErrors($client, $request, true));
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
                Log::warning($ex);
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
        $request = new Request(
            'POST', self::$URI . '/' . $amoBillID, $headers,
            json_encode([$preparedData])
        );
        self::handleErrors($client, $request, true);
    }
}
