<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use JsonException;

class ContactsPresendController extends Controller
{

    /**
     * @param $client
     * @param $contactDB
     * @return int
     * Description: return the AmoCRM contact ID
     */
    public function getAmoID($client, $contactDB): int
    {
        $contactID = $this->checkExists($client, $contactDB);
        if (!$contactID) {
            $contactID = $this->createAmo($client, ContactsPrepareController::prepare($contactDB));
        }
        return $contactID;
    }

    /**
     * @param $client
     * @param $contact
     * @return string|object
     * Description: Get the Contact ID using the request
     */
    private function checkExists($client, $contact): string|object
    {
        if (isset($contact['MOBIL_NYY'])){
            $query = '?query=' . $contact['MOBIL_NYY'];
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'][0]['id'];
            }
        }

        if (isset($contact['EMAIL'])){
            $query = '?query=' . ($contact['NOM'] ?? $contact['EMAIL']);
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'][0]['id'];
            }
        }

        if (isset($contact['FIO'])){
            $query = '?query=' . $contact['FIO'];
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'][0]['id'];
            }
        }
        return '';
    }

    /**
     * @param $client
     * @param $preparedContact
     * @return string|int
     * Description: returns the ID of AmoCRM contact
     */
    private function createAmo($client, $preparedContact): string|int
    {
        $res = ContactsRequestController::create($client, $preparedContact);
        if ($res) {
            try {
                $result = $res->getBody() !== '' ?
                    json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded']) {
                    return $result['_embedded']['contacts'][0]['id'];
                }
            }catch (JsonException $ex){
                Log::warning($ex->getMessage());
                Log::warning($ex->getFile());
                Log::warning($ex->getLine());
            }
        }
        return '';
    }
}
