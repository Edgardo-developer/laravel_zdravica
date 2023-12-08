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
    public function getTheContactID($client, $contactDB, $contactPrepared) : int{
        $contactID = $this->getContactId($client, $contactDB);
        if (!$contactID){
            $contactID = $this->createContactAmo($client, $contactDB);
        }
        return $contactID;
    }

    public function getTheLeadID($client, $DBLead) : int{
        $RequestExt = SendToAmoCRM::getRequestExt();
        $headers = $RequestExt['headers'];
        dd($DBLead);
        $query = '?query='.$DBLead['date'].' '.$DBLead['mobile'];
        $request = new Request('GET', self::$contactsURI.$query, $headers);
        $res = $client->sendAsync($request);
        if ($res->getStatusCode() === 400){
            SendToAmoCRM::updateAccess($client);
            return $this->createContactAmo($client, $DBLead);
        }
        try {
            $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
        }catch (\JsonException $exception){
            Log::log('1', $exception);
        }
            dd($result);
        if (isset($result) && $result['_embedded']){
            return $result['id'];
        }
        return 0;
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
        $query = '?query='.$contact['NOM'].' '.$contact['EMAIL'];
        $request = new Request('GET', self::$contactsURI.$query, $headers);
        $res = $client->sendAsync($request)->wait();
        if ($res->getStatusCode() === 204){
            return '';
        }
        if ($res->getStatusCode() === 400){
            SendToAmoCRM::updateAccess($client);
            return $this->getContactId($client, $contact);
        }
        try {
            $result = $res->getBody() !== '' ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
        }catch (\JsonException $exception){
            print_r($exception);
        }

        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts'][0]['id'];
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
        $request = new Request('POST', self::$contactsURI, $headers, json_encode($preparedContact));
        $res = $client->sendAsync($request)->wait();
        if ($res->getStatusCode() === 400){
            SendToAmoCRM::updateAccess($client);
            return $this->createContactAmo($client, $contactDB);
        }
        $result = $res->getBody() !== '' ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';;
        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts'][0]['id'];
        }
        return '';
    }
}
