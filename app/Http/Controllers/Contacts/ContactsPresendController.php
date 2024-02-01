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
    public function checkExists($client, $contact): string|object
    {
        $contacts = [];
        if (isset($contact['MOBIL_NYY']) && !$contacts){
            $query = '?query=' . $contact['MOBIL_NYY'];
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                $contacts = $result['_embedded']['contacts'];
            }
        }

        if (isset($contact['EMAIL']) && !$contacts){
            $query = '?query=' . ($contact['NOM'] ?? $contact['EMAIL']);
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                $contacts = $result['_embedded']['contacts'];
            }
        }

        if (isset($contact['FIO']) && !$contacts){
            $query = '?query=' . $contact['FIO'];
            $result = ContactsRequestController::get($client, $query);
            if ($result && $result['_embedded']) {
                $contacts = $result['_embedded']['contacts'];
            }
        }

        if ($contacts){
            if (count($contacts) > 1){
                $contactAmoID = $this->checkMultipleContacts($contacts,$contact);
                if ($contactAmoID){
                    return $contactAmoID;
                }
            }
            return $contacts[0]['id'];
        }

        return '';
    }

    private function checkMultipleContacts($contacts, $contact){
        if (isset($contact['FIO'])){
            $byFIO = $this->getByFIO($contacts,$contact['FIO']);
            if ($byFIO){ return $byFIO; }
        }

        if (isset($contact['agePat'])){
            $isChild = $contact['agePat'] <= 18;
            $byAge = $this->getByAge($contacts, $isChild);
            if ($byAge){ return $byAge; }
        }
        return 0;
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

    /**
     * @param array $amoContacts
     * @param bool $is_child
     * @return int
     */
    private function getByAge(array $amoContacts, bool $is_child) : int{
        foreach ($amoContacts as $amoContact){
            $customFields = $amoContact['custom_fields_values'];
            foreach ($customFields as $customField){
                if($customField['field_id'] === 391183){
                    $birthString = str_replace('.','/',$customField['values'][0]['value']);
                    $birthDay = date('Y',strtotime($birthString));
                    $now = date('Y');
                    $diff = $now - $birthDay;
                    if (($diff > 18 && !$is_child) || ($diff < 18 && $is_child)){
                        return $amoContact['id'];
                    }
                }
            }
        }
        return 0;
    }

    /**
     * @param array $amoContacts
     * @param string $FIO
     * @return int|mixed
     */
    private function getByFIO(array $amoContacts, string $FIO){
        foreach ($amoContacts as $amoContact){
            $customFields = $amoContact['custom_fields_values'];
            foreach ($customFields as $customField){
                if($customField['field_id'] === 391181){
                    if($customField['values'][0]['value'] === $FIO){
                        return $amoContact['id'];
                    }
                }
            }
        }
        return 0;
    }
}
