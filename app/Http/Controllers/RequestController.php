<?php

namespace App\Http\Controllers;

use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class RequestController extends Controller
{
    private static string $URI;
    private static string $client_id = '67eec975-12cb-46f7-ba04-edb4596d689b';
    private static string $client_secret = 'rUjL84GVG4ky0PUoJ9cWLMm1QbiQ9Mp5G0P24ArrUxG98ILEDFoHCKX8zsVfZtb4';
    private static string $grant_type = 'authorization_code';
    private static string $redirect = 'https://good-offer.ru';

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
            $send = $client->sendAsync($request)->wait();
            if($send->getStatusCode() === 401){
                self::updateAccess($client);
                return self::changeAndTryRequest($client, $request);
            }
            return $send;
        } catch (BadResponseException $ex) {
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            throw new \Exception('Job failed');
        }
    }

    /**
     * @return void
     * Description: update access token to the AmoCRM
     */
    protected static function updateAccess($client)
    {
        $getRequestExt = self::getRequestExt(true);
        $headers = $getRequestExt['headers'];
        $body = $getRequestExt['body'];
        try {
            $jsonData = json_encode($body, JSON_THROW_ON_ERROR);
            $request = new Request(
                'POST', 'https://zdravitsa.amocrm.ru/oauth2/access_token', $headers,
                $jsonData
            );
            $res = $client->sendAsync($request)->wait();
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            die();
        }


        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            die();
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
            try {
                $result = json_decode($output->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
                if ($result && $result['_embedded']) {
                    $leadRaw->amoLeadID = $result['_embedded']['leads'][0]['id'];
                    $leadRaw->save();
                }
            }catch (JsonException $ex){
                Log::warning($ex->getMessage());
                Log::warning($ex->getFile());
                Log::warning($ex->getLine());
            }
        }
    }
}
