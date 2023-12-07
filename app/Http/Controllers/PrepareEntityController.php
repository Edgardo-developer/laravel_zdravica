<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PrepareEntityController extends Controller
{
    // fields of Contact in the AmoCRM
    private static array $mergedContactFields = [
        'name',
        'first_name',
        'last_name',
//        'responsible_user_id',
        'created_by',
        'updated_by',
        'custom_fields_values'  => [
            170783 => 'mobile',
            170785 => 'email',
            391181 => 'FIO',
            391183 => 'Birthday',
            391185 => 'POL',
        ]
    ];

    // fields of the lead in the AmoCRM
    private $mergedLeadFields = [

    ];

    /**
     * @param array $contactDB
     * @return array
     * Description: prepares the array for the contact
     */
    public function prepareContact(array $contactDB) : array{
        $prepared = array();
        foreach (self::$mergedContactFields as $mergedContactField){
            if (is_string($mergedContactField)){
                $prepared[$mergedContactField] = $this->matchFieldsContact($mergedContactField, $contactDB);
            }else{
                foreach ($mergedContactField as $customFieldsKey => $customFieldsValue){
                    $prepared['custom_fields_values'][] = [
                        'field_id'  =>  $customFieldsKey,
                        'values'    =>  $this->matchFieldsContact($customFieldsValue, $contactDB),
                    ];
                }
            }
        }
        return $prepared;
    }

    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepareLead(array $leadDB, int $contactId) : array{

    }

    /**
     * @param string $mergedContactField
     * @param array $contactDB
     * @return mixed|string
     * Description: sets the new values
     */
    private function matchFieldsContact(string $mergedContactField, array $contactDB){
        return match($mergedContactField){
            'name'  => $contactDB['NOM'] . ' ' . $contactDB['PRENOM'],
            'first_name'  => $contactDB['NOM'],
            'last_name'  => $contactDB['PRENOM'],
            'created_by'  => $contactDB['created_at'],
            'updated_by'  => $contactDB['updated_at'],
            'mobile',   =>  $contactDB['MOBIL_NYY'],
            'email',    =>  $contactDB['EMAIL'],
            'FIO',  =>  $contactDB['NOM'] . ' ' . $contactDB['PRENOM'] . ' ' . $contactDB['PATRONYME'],
            'Birthday', =>  $contactDB['NE_LE'],
            'POL',  =>  $contactDB['POL'] ? 'Мужской' : 'Женский',
        };
    }
}
