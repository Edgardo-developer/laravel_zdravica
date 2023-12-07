<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class PresendEntityController extends Controller
{
    private static string $contactsURI = 'https://zdravitsa.amocrm.ru/api/v4/contacts';

    /**
     * @param $client
     * @param $contactDB
     * @return int
     * Description: return the AmoCRM contact ID
     */
    public function getTheContactID($client, $contactDB) : int{
        $contactID = $this->getContactId($client, $contactDB);

        if (!$contactID){
            $contactID = $this->createContactAmo($client, $contactDB);
        }
        return $contactID;
    }

    /**
     * @param $client
     * @param $contact
     * @return string|object
     * Description: Get the Contact ID using the request
     */
    private function getContactId($client, $contact) : string|object{
        $RequestExt = SendToAmoCRM::getRequestExt();
        $headers = $RequestExt['headers'];
        $query = '?query='.$contact['name'].' '.$contact['email'];
        $request = new Request('GET', self::$contactsURI.$query, $headers);
        $res = $client->sendAsync($request);

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

    /**
     * @param $client
     * @param $contactDB
     * @return string|int
     * Description: returns the ID of AmoCRM contact
     */
    private function createContactAmo($client, $contactDB) : string|int{
        $getRequestExt = SendToAmoCRM::getRequestExt();
        $headers = $getRequestExt['headers'];
        $preparedContact = (new PrepareEntityController)->prepareContact($contactDB);
        $request = new Request('POST', self::$contactsURI, $headers, $preparedContact);
        $res = $client->sendAsync($request)->wait();
        $result = $res->getBody();
        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts']['id'];
        }
        return '';
    }
}
