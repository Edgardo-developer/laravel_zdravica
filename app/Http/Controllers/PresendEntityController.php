<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PresendEntityController extends Controller
{
    public function getTheContactID($client, $clientDB){
        $contactID = $this->getClientAmo($client, $clientDB);

        if ($contactID){
            $contactID = $this->createContactAmo($client, $clientDB);
        }
        return $contactID;
    }
    private static $contactsURI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';
    private function getClientAmo($client, $contact) : string{
        $headers = [
            'Content-Type' => 'application/json',
            'Cookie' => 'session_id=bqujil3nfqfbncg3kue454vl30; user_lang=ru'
        ];
        $query = '?query='.$contact['name'].' '.$contact['email'];
        $request = new \GuzzleHttp\Psr7\Request('GET', self::$contactsURI.$query, $headers);
        $res = $client->sendAsync();

        try {
            $result = json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR);
        }catch (\JsonException $exception){
            Log::log('1', $exception);
        }

        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts']['id'];
        }
        return '';
    }

    private function createContactAmo($client, $contact){
        $headers = [
            'Content-Type' => 'application/json',
            'Cookie' => 'session_id=bqujil3nfqfbncg3kue454vl30; user_lang=ru'
        ];
        $request = new \GuzzleHttp\Psr7\Request('POST', self::$contactsURI, $headers);
    }
}
