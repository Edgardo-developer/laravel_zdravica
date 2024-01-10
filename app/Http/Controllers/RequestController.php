<?php

namespace App\Http\Controllers;

use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class RequestController extends Controller
{
    private static string $URI;
    private static string $client_id = '11f19f5a-df91-4ce9-86d8-0852a9eafd90';
    private static string $client_secret = 'IiTg2zPLLSfQfVlBc9edoN8Qn0WMJTQ0oT11S67Vx7gKFrhFCC2dvoVXvGcIgXch';
    private static string $grant_type = 'authorization_code';
    private static string $redirect = 'https://good-offer.ru/';

    public static function update($client, $preparedData)
    {
    }

    public static function get($client, $query)
    {
    }

    public static function delete($client, $amoID)
    {
    }

    protected static function handleErrors(Client $client, $request)
    {
        try {
            return $client->sendAsync($request)->wait();
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                self::updateAccess($client);
                return self::changeAndTryRequest($client, $request);
            }
        }
    }

    /**
     * @return void
     * Description: update access token to the AmoCRM
     * @throws JsonException
     */
    protected static function updateAccess($client)
    {
        $getRequestExt = self::getRequestExt(true);
        $headers = $getRequestExt['headers'];
        $body = $getRequestExt['body'];
        $request = new Request(
            'POST', 'https://zdravitsa.amocrm.ru/oauth2/access_token', $headers,
            json_encode($body, JSON_THROW_ON_ERROR)
        );
        $res = $client->sendAsync($request)->wait();


        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            Log::log(1, $e);
        }

        AmoCrmTable::query()->truncate();

        foreach ($result as $resultLineName => $resultLine) {
            AmoCrmTable::create([
                'key' => $resultLineName,
                'value' => $resultLine
            ]);
        }
    }

    /**
     * @param bool $refreshToken
     * @return array
     * Description: Method generates body and headers for request
     */
    public static function getRequestExt(bool $refreshToken = false)
    {
        if (!$refreshToken) {
            $token = AmoCrmTable::all()->where('key', '=', 'access_token')?->first();
            if ($token) {
                $token = $token->toArray();
                $headers = [
                    'Content-Type' => 'application/json',
                    'Cookie' => 'user_lang=ru',
                    'Authorization' => $token ? 'Bearer ' . $token['value'] : '',
                ];
            }
        } else {
            $token = AmoCrmTable::all()->where('key', '=', 'refresh_token')->first()->toArray()['value'];
            $headers = [
                'Content-Type' => 'application/json',
                'Cookie' => 'user_lang=ru'
            ];
        }

        $body = [
            'client_id' => self::$client_id,
            'client_secret' => self::$client_secret,
            'grant_type' => self::$grant_type,
            'code' => $token,
            'redirect_uri' => self::$redirect,
        ];

        return [
            'headers' => $headers,
            'body' => $body,
        ];
    }

    public static function create($client, $preparedData)
    {
    }

    private static function changeAndTryRequest(Client $client, Request $request)
    {
        $getRequestExt = self::getRequestExt();
        $request->withHeader('Authorization', $getRequestExt['headers']['Authorization']);
        return $client->sendAsync($request)->wait();
    }

    protected static function handleSuccess($output, $leadRaw)
    {
        if ($output) {
            $result = json_decode($output->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
            if ($result && $result['_embedded']) {
                $leadRaw->amoLeadID = $result['_embedded']['leads'][0]['id'];
                $leadRaw->save();
            }
        }
    }
}
