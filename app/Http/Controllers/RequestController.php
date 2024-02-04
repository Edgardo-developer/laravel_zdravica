<?php

namespace App\Http\Controllers;

use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;
use JsonException;

class RequestController extends Controller
{
    private static string $client_id = '11f19f5a-df91-4ce9-86d8-0852a9eafd90';
    private static string $client_secret = 'IiTg2zPLLSfQfVlBc9edoN8Qn0WMJTQ0oT11S67Vx7gKFrhFCC2dvoVXvGcIgXch';
    private static string $grant_type = 'refresh_token';
    private static string $redirect = 'https://good-offer.ru/';
    private static string $refresh_tokens = 'def50200e1dcb8e6c2e57579d9a46ab443750b8d74a97c79d8f4c7e0216d94e5c9191e01a2d0c2b0ef3a047cbbc939f3f37e635d270fc0a4fe0e90341e5fb2b746f803f9c38333939157f182ec8dc95ece556fe510f23bf3bcaf94b960603193d85a90d77b9484787c302fb2255bfcbfdc21e57447d973866bc981fbf060d478840acd206d9708b7270cc66424733e1996f903cdc80282894b8fe643ec2e7c7c5d95e66cb30ec9bf126da1f4c1cbb522f623a5e40e6b94769c030f8c4caac8347a7259ef9b2850c16d7cff08345866ff6a74712ec64630b37c78223230492369e0f7de7caa04562a5b13350d79d5d18cb571f640f46a87306ba3fc58ea06cef903124e3b9c0043f87ae52dc1bf9874f0785de0cc079d7b8c64e79e246e036ec245fa0b016dfe025e1e55d9b12427b3f51a0e4689e4ddefb9992d57e0f5a6d8d203a32c090462debdd5a77af4c6556cf856f27a4abc12f1356702c388de827a4ad896733190e2ce91ae4252076b1d192a3f3f786c215361332736796eee4d665ea90ac8966f05d9eaf8224dadaf8dd891fddf99ff4be5d140e0845d488266cb138dfb59a1fc4cb0929698c58dcce1c651cb6e5ebe85469be5b5483d60d76f5d24669eda564c4865f67203b7d3595d93adc65b1ac42f41b658f928fc02ed973735cb21316e38a492e680d6ab743796';

    protected static function handleErrors(Client $client, $request)
    {
        try {
            return $client->sendAsync($request)->wait();
        } catch (RequestException $ex) {
            if ($ex->getCode() === 401) {
                self::updateAccess($client);
                return self::changeAndTryRequest($client, $request);
            }

            Log::warning($ex->getMessage());
            Log::warning($ex->getFile());
            Log::warning($ex->getCode());
            Log::warning($ex->getLine());
            return [];
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
            return;
        }


        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return;
        }

        foreach ($result as $resultLineName => $resultLine) {
            AmoCrmTable::updateOrCreate([
                'key' => $resultLineName,
            ],
            [
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
            $token = AmoCrmTable::where('key', '=', 'access_token')->get()->first();
            $headers = [
                'Content-Type' => 'application/json',
                'Cookie' => 'user_lang=ru',
                'Authorization' => 'Bearer ' . ($token ? $token->value : ''),
            ];
        } else {
            $token = AmoCrmTable::where('key', '=', 'refresh_token')->get()->first()->value;
            $headers = [
                'Content-Type' => 'application/json',
                'Cookie' => 'user_lang=ru'
            ];
        }

        $body = [
            'client_id' => self::$client_id,
            'client_secret' => self::$client_secret,
            'grant_type' => self::$grant_type,
            'refresh_token' => $token,
            'redirect_uri' => self::$redirect,
        ];

        return [
            'headers' => $headers,
            'body' => $body,
        ];
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

    private static function changeAndTryRequest(Client $client, Request $request)
    {
        $getRequestExt = self::getRequestExt();
        $request->withHeader('Authorization', $getRequestExt['headers']['Authorization']);
        return $client->sendAsync($request)->wait();
    }
}
