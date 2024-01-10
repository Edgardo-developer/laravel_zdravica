<?php

namespace App\Http\Controllers\Bill;


use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class BillRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/catalogs/12352/elements';

    public static function create($client, $preparedData): string
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('POST', self::$URI, $headers, json_encode([$preparedData]));
        return self::validateCreateResponse(self::handleErrors($client, $request, true));
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
                Log::warning($ex);
            }
        }
        return 0;
    }

    public static function update($client, $preparedData): void
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $amoBillID = $preparedData['amoBillID'];
        unset($preparedData['amoBillID']);
        $request = new Request(
            'POST', self::$URI . '/' . $amoBillID, $headers,
            json_encode([$preparedData])
        );
        self::handleErrors($client, $request, true);
    }
}
