<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SendToAmoCRM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadPresendController extends Controller
{
    private static string $leadURI = 'https://zdravitsa.amocrm.ru/api/v4/leads';

    public function getAmoID($client, $DBLead) : int{
        $RequestExt = SendToAmoCRM::getRequestExt();
        $headers = $RequestExt['headers'];
        $query = '?query='.$DBLead['amoContactID'];
        $request = new \GuzzleHttp\Psr7\Request('GET', self::$leadURI.$query, $headers);
        $res = $client->sendAsync($request)->wait();
        if ($res->getStatusCode() === 401){
            SendToAmoCRM::updateAccess($client);
            return $this->getAmoID($client, $DBLead);
        }
        try {
            $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
        }catch (\JsonException $exception){
            Log::debug($exception);
        }
        if (isset($result) && $result['_embedded']){
            foreach ($result['_embedded']['leads'] as $lead){
                if (!$lead['custom_fields_values']){
                    return $lead['id'];
                }
            }
        }
        return 0;
    }
}
