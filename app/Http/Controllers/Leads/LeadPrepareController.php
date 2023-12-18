<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\PrepareEntityController;

class LeadPrepareController extends PrepareEntityController
{
    // fields of the lead in the AmoCRM
    private static array $amoFields = [
        'name',
        'price',
        'responsible_user_id',
        'custom_fields_values'  =>  [
            454373  => "direction",
            454375  => "filial",
            454379  => "fioDoc",
            454381  => "offers",
            454377  => "specDoc",
            1571881  => "date",
            1571885 => "declareVisit",
            1572983 => "responsibleFIO",
        ],
    ];

    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public static function prepare(array $leadDB, int $contactId) : array{
        $prepared = ['_embedded' =>  ['contacts'  => [['id'    => $contactId]]]];
        foreach (self::$amoFields as $fieldValue){
            if ('responsible_user_id' === $fieldValue){
                continue;
            }
            if (is_string($fieldValue)){
                $prepared[$fieldValue] = self::matchFields($fieldValue, $leadDB);
            }else{
                foreach ($fieldValue as $subFieldKey => $subFieldValue){
                    $prepared['custom_fields_values'][] = [
                        "field_id"  =>  $subFieldKey,
                        "values"    =>  [["value" =>  self::matchFields($subFieldValue, $leadDB)]]
                    ];
                }
            }
        }
        return $prepared;
    }

    /**
     * @param string $mergedLeadFields
     * @param array $leadDB
     * @return mixed|string
     * Description: sets the new values
     */
    private static function matchFields(string $mergedLeadFields, array $leadDB){
        return match($mergedLeadFields){
            'name'  => $leadDB['leadDBId'],
            'price'  => (integer)$leadDB['billSum'],
            "direction"  => $leadDB['direction'],
            "filial"    => $leadDB['filial'],
            "fioDoc"  => $leadDB['fioDoc'],
            "offers"    => $leadDB['offers'],
            "specDoc"    => $leadDB['specDoc'],
//            "responsible_user_id"    => $leadDB['responsible_user_id'] === "NULL" ? 10182090 : $leadDB['responsible_user_id'],
            "date"    => $leadDB['date'],
            "responsibleFIO"    => $leadDB['responsibleFIO'],
            "declareVisit" => (int)$leadDB['declareVisit'] === 1,
        };
    }
}
