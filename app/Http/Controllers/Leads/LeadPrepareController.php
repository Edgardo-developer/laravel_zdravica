<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PrepareEntityController;
use Illuminate\Http\Request;

class LeadPrepareController extends PrepareEntityController
{
    // fields of the lead in the AmoCRM
    private static array $amoFields = [
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
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepare(array $leadDB, int $contactId) : array{
        $prepared = ['_embedded' =>  ['contacts'  => [['id'    => $contactId]]]];
        foreach (self::$amoFields as $fieldValue){
            if (is_string($fieldValue)){
                $prepared[$fieldValue] = $this->matchFields($fieldValue, $leadDB);
            }else{
                foreach ($fieldValue as $subFieldKey => $subFieldValue){
                    $prepared['custom_fields_values'][] = [
                        "field_id"  =>  $subFieldKey,
                        "values"    =>  [["value" =>  $this->matchFields($subFieldValue, $leadDB)]]
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
    private function matchFields(string $mergedLeadFields, array $leadDB){
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
