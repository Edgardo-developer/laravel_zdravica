<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\PrepareEntityController;

class LeadLinksPrepareController extends PrepareEntityController
{
    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public static function prepare(array $leadDB, int $contactId) : array{
        return ["_embedded" => [
            "links" => [
                [
                    "to_entity_id"=> 414593,
                    "to_entity_type"=> "catalog_elements",
                    "metadata"=> [
                    "quantity"=> 1.0,
                        "catalog_id"=> $leadDB['amoLeadID']
                    ]
                ],
                [
                    "to_entity_id" => $contactId,
                    "to_entity_type"=> "contacts",
                    "metadata"=> [
                        "main_contact"=> true
                    ]
                ]
            ]
        ]
    ];
    }
}
