<?php

namespace App\Http\Controllers;

class PrepareEntityController extends Controller
{
    // fields of Contact in the AmoCRM
    private static array $mergedContactFields = [
        'name',
        'first_name',
        'last_name',
//        'responsible_user_id',
//        'created_by',
//        'updated_by',
        'custom_fields_values'  => [
            170783 => 'mobile',
            170785 => 'email',
            391181 => 'FIO',
            391183 => 'Birthday',
            391185 => 'POL',
        ]
    ];

    // fields of the lead in the AmoCRM
    private static array $mergedLeadFields = [
        'name',
        'price',
//        'responsible_user_id',
        'custom_fields_values'  =>  [
            454373  => "direction",
            454375  => "filial",
            454379  => "fioDoc",
            454381  => "offers",
            454377  => "specDoc",
            1571881  => "date",
            1571885 => "declareVisit"
        ],
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
                        'values'    =>  [['value'=> $this->matchFieldsContact($customFieldsValue, $contactDB)]],
                    ];
                }
            }
        }
        return array($prepared);
    }

    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepareLead(array $leadDB, int $contactId) : array{
        $prepared = ['_embedded' =>  ['contacts'  => [['id'    => $contactId]]]];
        foreach (self::$mergedLeadFields as $fieldValue){
            if (is_string($fieldValue)){
                $prepared[$fieldValue] = $this->matchFieldsLead($fieldValue, $leadDB);
            }else{
                foreach ($fieldValue as $subFieldKey => $subFieldValue){
                    $prepared['custom_fields_values'][] = [
                        "field_id"  =>  $subFieldKey,
                        "values"    =>  [["value" =>  $this->matchFieldsLead($subFieldValue, $leadDB)]]
                    ];
                }
            }
        }
        return $prepared;
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

    /**
     * @param string $mergedContactField
     * @param array $contactDB
     * @return mixed|string
     * Description: sets the new values
     */
    private function matchFieldsLead(string $mergedLeadFields, array $leadDB){
        return match($mergedLeadFields){
            'name'  => $leadDB['leadDBId'] ?? 125,
            'price'  => (integer)$leadDB['billSum'],
            "direction"  => $leadDB['direction'],
            "filial"    => $leadDB['filial'],
            "fioDoc"  => $leadDB['fioDoc'],
            "offers"    => $leadDB['offers'],
            "specDoc"    => $leadDB['specDoc'],
            "date"    => $leadDB['date'],
            "declareVisit" => (int)$leadDB['declareVisit'] === 1,
        };
    }
}
