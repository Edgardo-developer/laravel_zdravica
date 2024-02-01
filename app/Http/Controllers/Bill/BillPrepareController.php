<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillPrepareController extends Controller
{
    private static array $amoFields = [
        'custom_fields_values' => [
            'status' => 1550048,
            'account' => 1550052,
            'offers' => 1550058,
            'price' => 1550062,
        ],
    ];

    public function __construct($billDB, $billStatus){
        $this->billDB = $billDB;
        $this->billStatus = $billStatus;
    }

    public function prepare(): array
    {
        $billDB = $this->billDB;
        $arr = [
            'custom_fields_values' => [],
        ];

        $arr = $this->accross_fields($arr,$billDB);

        if ($this->billStatus === 1) {
            $arr['custom_fields_values'][] = [
                'field_id' => 1550056,
                'values' => [
                    'value' => time(),
                ]
            ];
        }
        return $arr;
    }

    /**
     * @param $arr
     * @param $billDB
     * @return array
     * Description: Walk across all fields
     */
    private function accross_fields($arr,$billDB){
        foreach (self::$amoFields['custom_fields_values'] as $amoFieldName => $amoFieldID) {
            if (isset($billDB[$amoFieldName])) {
                if ($amoFieldName === 'account' && $billDB[$amoFieldName]['entity_id'] === 0){
                    continue;
                }
                $locArr = [
                    'field_id' => $amoFieldID,
                    'values' => $amoFieldName !== 'offers' ?
                        [['value' => $billDB[$amoFieldName]]] :
                        self::modifyOffers($billDB[$amoFieldName])
                ];

                $arr['custom_fields_values'][] = $locArr;
            }
        }
        return $arr;
    }

    /**
     * @param $offers
     * @return array
     * Description: offers adds to the bill
     */
    private static function modifyOffers($offers): array
    {
        $offersArr = [];
        foreach ($offers['offerNames'] as $k => $offerPrice) {
            $offersArr[] = [
                'value' => [
                    'description' => $offerPrice,
                    'unit_price' => $offers['offerPrices'][$k],
                    'quantity' => 1,
                ]
            ];
        }
        return $offersArr;
    }
}
