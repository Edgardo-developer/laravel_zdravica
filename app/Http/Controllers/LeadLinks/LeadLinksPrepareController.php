<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\PrepareEntityController;

class LeadLinksPrepareController extends PrepareEntityController
{
    /**
     * @param array $leadDB
     * @param int $AmoBillID
     * @return array
     * Description: prepares the array of the lead
     */
    public static function prepare(array $leadDB, int $AmoBillID): array
    {
        return [
            [
                'to_entity_id' => $AmoBillID,
                'to_entity_type' => 'catalog_elements',
                'metadata' => [
                    'quantity' => 1.0,
                    'catalog_id' => 12352
                ]
            ],
            [
                'to_entity_id' => $leadDB['amoContactID'],
                'to_entity_type' => 'contacts',
            ]
        ];
    }

    public static function prepareAll(array $ids): array
    {
        $arr = [];
        foreach ($ids as $id) {
            $arr[] = [
                'to_entity_id' => (int)$id,
                'to_entity_type' => 'catalog_elements',
                'metadata' => [
                    'catalog_id' => 12348
                ]
            ];
        }
        return $arr;
    }
}
