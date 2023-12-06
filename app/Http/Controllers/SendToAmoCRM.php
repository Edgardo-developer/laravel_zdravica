<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SendToAmoCRM extends Controller
{
    private static $client_id = "11f19f5a-df91-4ce9-86d8-0852a9eafd90";
    private static $client_secret = "IiTg2zPLLSfQfVlBc9edoN8Qn0WMJTQ0oT11S67Vx7gKFrhFCC2dvoVXvGcIgXch";
    private static $grant_type = "authorization_code";
    private static $code = "def5020099ca8fa67c97a0dfa685ad1910bc0680373544eeef6e4720dfbcaba7ccd6a46f54a753dee9125246c73374c1806467aa39af8cc1bf5d0ee49d3638bb11fc593521255bf6810e6cb2f84c652e6dad52894240615ea090f1fc8f362a12c774afdf14fcbf54a2fd62163678622c7787f731f10886012c0a5dfbb8dadaa81ea59b4f763574ff7d2ca8b4e6c43749a532ca291077bca194f01c0dc20001c8a3c129919898be0de8ab4dc07809929376d8433fa2bb6de2e0d5e413d0b3079717ec5e3fcac58bdfa370eb418f36b62054681357f360c95f785e6994b5134b2164c361aeaae021e3d2cda588635d43a3c94811c554876134a4a07486867640deab741fcca8c04fca28c57241029c5decad893718573cde6e7158010af248247c3013bf127a63c333fcd11603b9b8ffb4475fd94b69ca816d1bccaa4bdf6ddf97025aea433e8912dcd2abf0c9829da00e7b95ec8db429a34142a7dcfe1a1d3e94348f364da9cc5ddf660b4acc60f52f48de54d54b18d6746efdb2753e8a70580bbc8c3ce17be6493e1442e960286d5f891b2775f9664a8fd15621c2d63b421b25aec8909d8e5907bad783b7040278b92750b6805c741dff8f7fbe53f72390d97db5e811ede63df61ce470c5b14c0844a67ca326fe3b330dc19d5775092ea28eb13888114c2d981521";
    private static $redirect = "https://good-offer.ru/";

    public function sendDealToAmoCRM(){
        $builderEntity = [BuilderEntityController::class, 'buildEntity'];
    }

    private function connectToAmoCRM(){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://zdravitsa.amocrm.ru/oauth2/access_token',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
            "client_id": '.self::$client_id.',
            "client_secret": '.self::$client_secret.',
            "grant_type": '.self::$grant_type.',
            "code": '.self::$code.',
            "redirect_uri": '.self::$redirect.',
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: user_lang=ru'
            ),
        ));

        $response = json_decode(curl_exec($curl), 'true');

        if (!$response['token_type']){
            $this->refreshTheToken($curl);
        }
        return $curl;
    }

    private function refreshTheToken(){

    }
}
