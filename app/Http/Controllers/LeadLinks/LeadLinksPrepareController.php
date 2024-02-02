<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\Controller;

class LeadLinksPrepareController extends Controller
{

    public function __construct(){

    }

    /**
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepare(array $leadDB, $AmoBillID = 0): array
    {
        $bodyData  = [
            [
                'to_entity_id' => (int)$leadDB['amoContactID'],
                'to_entity_type' => 'contacts',
            ]
        ];
        if ($AmoBillID){
            $bodyData[] =  [
                'to_entity_id' => (int)$AmoBillID,
                'to_entity_type' => 'catalog_elements',
                'metadata' => [
                    'quantity' => 1.0,
                    'catalog_id' => 12352
                ]
            ];
        }
        return $bodyData;
    }

    public function prepareAll(array $ids): array
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
