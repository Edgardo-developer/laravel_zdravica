<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\PrepareEntityController;

class LeadLinksPrepareController extends PrepareEntityController
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
            1581797  => "date",
//            1571885 => "declareVisit",
            1572983 => "responsibleFIO",
        ],
    ];

    /**
     * @param array $leadDB
     * @param int $AmoBillID
     * @return array
     * Description: prepares the array of the lead
     */
    public static function prepare(array $leadDB, int $AmoBillID) : array{
        return [
            [
                "to_entity_id"=> $AmoBillID,
                "to_entity_type"=> "catalog_elements",
                "metadata"=> [
                "quantity"=> 1.0,
                    "catalog_id"=> 12352
                ]
            ],
            [
                "to_entity_id" => $leadDB['amoContactID'],
                "to_entity_type"=> "contacts",
            ]
        ];
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
            "responsible_user_id"    => $leadDB['responsible_user_id'] === "NULL" ? 10182090 : $leadDB['responsible_user_id'],
            "date"    => strtotime($leadDB['date']),
            "responsibleFIO"    => $leadDB['responsibleFIO'],
            "declareVisit" => (int)$leadDB['declareVisit'] === 1,
        };
    }
}
