<?php

namespace App\Http\Controllers\ToAmo;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use JrBarros\CheckSSL;

class GuzzleToAmo extends Controller
{
    private string $clientSecret = 'IiTg2zPLLSfQfVlBc9edoN8Qn0WMJTQ0oT11S67Vx7gKFrhFCC2dvoVXvGcIgXch';
    private string $clientId = '11f19f5a-df91-4ce9-86d8-0852a9eafd90';
    private string $redirectUri = 'https://good-offer.ru/';

    public function sendLeadToAmo(){
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient($this->clientId, $this->clientSecret, $this->redirectUri);
        $accessToken = new \League\OAuth2\Client\Token\AccessToken(
            ['access_token'=>'def50200a2f50c7e8093fd8174b59a786aaa83a9a8a660f2ad899a90391f576797e6d9c3460bcd5c23460422e041ad9249272c88341c56fb1d139f6ea2f3d2fa75bb5ffecaf113db872173ac01772c6de766cc4ed72f8b58741b9dd17220e8904557c5256905a85f128ab085976fcdb15ccb8eae30e6f41c6cf70002367f832cb135dda0cac58104ae87cfa94fa9dd5b87a6745f63af4873ad1e9bd341660f4dcf43b6d6aab99bec9516a2e50305e294b18c06c3966fda1cb684d43df922ce9f2fc4252d63954ba3b03219dfc3e8b8177391c278df34ca3666cc9719a4597e3af131330645932ecfef05d9bc98bd8ac7ff2982e9cbef7ff59c332483177d35e70812d62a514290a0420c94d04df10087715e39a7cd616384f6c4d822fcf1bda3ecf7f179d0fbf9449ee96787b74c92025620462e233884efaa22156592f165a061c48682d5c915c4409e3ff60f78e4322a802f6f011ecf6d1d9b3095bad119d4a040ccd5d0f99ba6de5ca000c9340f8f41b68f2b76ecdec85d32aeef6f981c20d448c9c0fe4cc28df55725f9816e8833b31254a1ecfde65160eccabc9ce0df558638c1319f9440f76a7068c7db89141b893304b3ef00bee1556c05a8e4c3d0e93684da92257a69cd3f99cf40694ae3f6430b0ecd74232faa587c8a16deaa5f26272e95282557e67b',
                'baseDomain'    => 'zdravitsa.amocrm.ru',
                'expires'   => '200000',
                ]);
        $apiClient->setAccessToken($accessToken)
            ->setAccountBaseDomain($accessToken->getValues()['baseDomain'])
            ->onAccessTokenRefresh(
                function (\League\OAuth2\Client\Token\AccessTokenInterface $accessToken, string $baseDomain) {
                    saveToken(
                        [
                            'accessToken' => $accessToken->getToken(),
                            'refreshToken' => $accessToken->getRefreshToken(),
                            'expires' => $accessToken->getExpires(),
                            'baseDomain' => $baseDomain,
                        ]
                    );
                });
        dd($apiClient->leads()->getOne('2727819'));
    }
}
