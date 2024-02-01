<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\RequestController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class ContactsRequestController extends RequestController
{
    private static string $URI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';

    public function create($client, $preparedData)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        try {
            $jsonData = json_encode($preparedData, JSON_THROW_ON_ERROR);
            $request = new Request('POST', self::$URI, $headers, $jsonData);
            return self::handleErrors($client, $request, true);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return false;
        }
    }

    public function update($client, $preparedData = null)
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $amoID = $preparedData['amoID'];
        unset($preparedData['amoID']);
        try {
            $request = new Request('PATCH', self::$URI . '/' . $amoID, $headers,
                json_encode($preparedData[0], JSON_THROW_ON_ERROR)
            );
            return self::handleErrors($client, $request, true);
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return false;
        }
    }

    public function get($client, $query): array
    {
        $RequestExt = self::getRequestExt();
        $headers = $RequestExt['headers'];
        $request = new Request('GET', self::$URI . $query, $headers);
        $res = self::handleErrors($client, $request, true);
        if ($res) {
            try {
                $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                return $result ?? [];
            } catch (JsonException $ex) {
                Log::warning($ex->getMessage());
                Log::warning($ex->getFile());
                Log::warning($ex->getTraceAsString());
                Log::warning($ex->getLine());
            }
        }
        return [];
    }
}
