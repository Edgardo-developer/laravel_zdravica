<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class ContactsPresendController extends Controller
{
    private ContactsRequestController $ContactsRequestController;

    public function __construct($client)
    {
        $this->client = $client;
        $this->ContactsRequestController = new ContactsRequestController();
    }

    /**
     * @param array $contact
     * @return array
     */
    public function checkExistsByNumber(array $contact) : array{
        if (isset($contact['MOBIL_NYY']) && (int)$contact['MOBIL_NYY'] > 0){
            $query = '?query=' . $contact['MOBIL_NYY'];
            $result = $this->get($query);
            if ($result && $result['_embedded']) {
                return $result['_embedded']['contacts'];
            }
        }
        return [];
    }

    /**
     * @param string $contactEMAIL
     * @return array
     */
    public function checkExistsByEMAIL(string $contactEMAIL) : array{
        $query = '?query=' . $contactEMAIL;
        $result = $this->get($query);
        if ($result && $result['_embedded']) {
            return $result['_embedded']['contacts'];
        }
        return [];
    }

    /**
     * @param string $contactFIO
     * @return array
     */
    public function checkExistsByFIO(string $contactFIO) : array{
        $query = '?query=' . $contactFIO;
        $result = $this->get($query);
        if ($result && $result['_embedded']) {
            return $result['_embedded']['contacts'];
        }
        return [];
    }

    /**
     * @param array $contactDB
     * @param array $contacts
     * @return int
     * Description: Get the Contact ID using the request
     */
    public function checkExists(array $contactDB,array $contacts): int
    {
        if (count($contacts) > 0){
            $contactAmoID = $this->checkMultipleContacts($contacts,$contactDB);
            return $contactAmoID ?? 0;
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
            if ((int)$byFIO > 0){
                return $byFIO;
            }
        }

        if (isset($contactDB['NE_LE'])){
            $byAge = $this->getByAge($contacts, $contactDB);
            if ((int)$byAge > 0){
                return $byAge;
            }
        }
        return 0;
    }

    /**
     * @param array $amoContacts
     * @param array $contactDB
     * @return int
     */
    private function getByAge(array $amoContacts, array $contactDB) : int{
        foreach ($amoContacts as $amoContact){
            $customFields = $amoContact['custom_fields_values'];
            foreach ($customFields as $customField){
                if($customField['field_id'] === 391183){

                    $birthDay = date('d.m.Y',strtotime($customField['values'][0]['value']));
                    $contactDBDateBirthTime = date('d.m.Y', strtotime($contactDB['NE_LE']));
                    if ($contactDBDateBirthTime === $birthDay){
                        return $amoContact['id'];
                    }
//                    $birthString = str_replace('.','/',$customField['values'][0]['value']);
//                    $birthDay = date('Y',strtotime($birthString));
//                    $now = date('Y');
//                    $diff = $now - $birthDay;
//                    if (($diff > 18 && !$is_child) || ($diff < 18 && $is_child)){
//                        return $amoContact['id'];
//                    }
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
