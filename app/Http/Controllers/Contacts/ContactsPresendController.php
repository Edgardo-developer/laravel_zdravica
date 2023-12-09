<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PrepareEntityController;
use App\Http\Controllers\SendToAmoCRM;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

class ContactsPresendController extends Controller
{

    /**
     * @param $client
     * @param $contactDB
     * @return int
     * Description: return the AmoCRM contact ID
     */
    public function getAmoID($client, $contactDB, $contactPrepared) : int{
        $contactID = $this->checkExists($client, $contactDB);
        if (!$contactID){
            $contactID = $this->createAmo($client, $contactDB);
        }
        return $contactID;
    }

    /**
     * @param $client
     * @param $contact
     * @return string|object
     * Description: Get the Contact ID using the request
     */
    private function checkExists($client, $contact) : string|object{
        $query = '?query='.$contact['NOM'].' '.$contact['EMAIL'];
        $result = RequestController::get($client, $query);
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
     * @throws \JsonException
     */
    private function createAmo($client, $contactDB) : string|int{
        $preparedContact = (new PrepareEntityController)->prepareContact($contactDB);
        $res = RequestController::create($client, $preparedContact);
        if ($res->getStatusCode() === 401){
            return $this->createAmo($client, $contactDB);
        }
        $result = $res->getBody() !== '' ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';;
        if (isset($result) && $result['_embedded']){
            return $result['_embedded']['contacts'][0]['id'];
        }
        return '';
    }
}
