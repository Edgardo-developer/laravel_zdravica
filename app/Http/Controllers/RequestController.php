<?php

namespace App\Http\Controllers;

use App\Models\AmoCrmTable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use JsonException;

class RequestController extends Controller
{
    private static string $client_id = '11f19f5a-df91-4ce9-86d8-0852a9eafd90';
    private static string $client_secret = 'IiTg2zPLLSfQfVlBc9edoN8Qn0WMJTQ0oT11S67Vx7gKFrhFCC2dvoVXvGcIgXch';
    private static string $grant_type = 'refresh_token';
    private static string $redirect = 'https://good-offer.ru/';
    private static string $refresh_tokens = 'def502000fd83433ac48b987bf738d23d9be77227d3675e1d3e350ccd31e6df3f6bd99c14d5768e91be45e3b62710004e7cb3c93ec1454906218bea8a15c62a78672610b6caa48b96a08eb42587ae93f525818d6843e433aaf0f264967f44be808e9418f605faf17455c477b65fb7f63425e312a95edb23581c3171ebe655e4bcec9dbaffdf0bacfca356f0d638da495ff6cd063a4d64dce78cf3d20c8cf94f3c6669f2fb31f383149520ecf6706bd78c7f39dd880276ac8f2f351e317174aa3a732368e4098c22bb85dcac23d608b52ed4240be8cc72a1d563526b2549ea7eb64e7f6ec2beae842a778c84fe4eece1743746c2d3df3ad72809373c1803c48720095bf70e73b9f179844cb797f32703a26523948ee0d17cf1625e0a84dc8e18c081db7fed1e292c32df9da30db488aa3f9ff1a9b79d4426819ca1a701254a043b16c2a19d7f598f1d18364b3a86a85488f25ff7400a9251c521a61d1ec508d345282b6303015a47b4388621131926f226f6fca756a3dd68b75e1d871208eb6ed24a23e6a6594dcb9ec8129bdefd74ea042cceff6584956be341697da80382f0f5290dcc5f7a443c88aff643c8266081e5a211cf0870214b5f078a547a3752c97b3c58c9833160969bc1e164cf59234794874388bd2ab7252e477e14311dab607b80c8262d4c19843f076c9c366ee';

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
     * @param $client
     * @return Response|array Description: update access token to the AmoCRM
     * Description: update access token to the AmoCRM
     */
    public static function updateAccess($client) : Response|array
    {
        $getRequestExt = self::getRequestExt(true);
        $headers = $getRequestExt['headers'];
        $body = $getRequestExt['body'];
        try {
            $jsonData = json_encode($body, JSON_THROW_ON_ERROR);

            try {
                $request = new Request(
                    'POST', 'https://zdravitsa.amocrm.ru/oauth2/access_token', $headers,
                    $jsonData
                );
                $res = $client->sendAsync($request)->wait();
            }catch (RequestException $exception){
//                Log::warning($exception->getMessage());
                Log::warning($exception->getCode());
                Log::info($jsonData);
                return [];
            }
        }catch (JsonException $ex){
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return [];
        }


        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $ex) {
            Log::warning($ex->getMessage());
            Log::warning($ex->getTraceAsString());
            Log::warning($ex->getLine());
            return [];
        }

        foreach ($result as $resultLineName => $resultLine) {
            AmoCrmTable::updateOrCreate([
                'key' => $resultLineName,
            ],
            [
                'value' => $resultLine
            ]);
        }
        return $res;
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
