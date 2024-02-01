<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use JsonException;

class ContactsPresendController extends Controller
{
    private ContactsRequestController $ContactsRequestController;

    public function __construct($client)
    {
        $this->client = $client;
        $this->ContactsRequestController = new ContactsRequestController();
    }

    /**
     * @param $contact
     * @return array
     */
    public function checkExistsByNumber($contact) : array{
        if (isset($contact['MOBIL_NYY'])){
            $query = '?query=' . $contact['MOBIL_NYY'];
            $result = $this->get($query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'];
            }
        }
        return [];
    }

    /**
     * @param $contact
     * @return array
     */
    public function checkExistsByEMAIL($contact) : array{
        if (isset($contact['MOBIL_NYY'])){
            $query = '?query=' . $contact['MOBIL_NYY'];
            $result = $this->get($query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'];
            }
        }
        return [];
    }

    /**
     * @param $contact
     * @return array
     */
    public function checkExistsByFIO($contact) : array{
        if (isset($contact['FIO'])){
            $query = '?query=' . $contact['FIO'];
            $result = $this->get($query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'];
            }
        }
        return [];
    }

    /**
     * @param $contactDB
     * @param $contacts
     * @return int
     * Description: Get the Contact ID using the request
     */
    public function checkExists($contactDB, $contacts): int
    {
        if ($contacts){
            if (count($contacts) > 1){
                $contactAmoID = $this->checkMultipleContacts($contacts,$contactDB);
                if ($contactAmoID){
                    return $contactAmoID;
                }
            }
            return $contacts[0]['id'];
        }

        return 0;
    }

    /**
     * @param $contacts
     * @param $contactDB
     * @return int|mixed
     */
    private function checkMultipleContacts($contacts, $contactDB){
        if (isset($contactDB['FIO'])){
            $byFIO = $this->getByFIO($contacts,$contactDB['FIO']);
            if ($byFIO){ return $byFIO; }
        }

        if (isset($contactDB['agePat'])){
            $isChild = $contactDB['agePat'] <= 18;
            $byAge = $this->getByAge($contacts, $isChild);
            if ($byAge){ return $byAge; }
        }
        return 0;
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

    public function get($query): array
    {
        return $this->ContactsRequestController->get($this->client,$query);
    }
}
