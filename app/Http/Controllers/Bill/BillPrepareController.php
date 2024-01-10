<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\PrepareEntityController;

class BillPrepareController extends PrepareEntityController
{
    private static array $amoFields = [
        'custom_fields_values'  =>  [
            "status" => 1550048,
            "account" => 1550052,
            "offers" => 1550058,
            "price" => 1550062,
        ],
    ];

    public static function prepare(array $billDB, int $billStatus) : array{
        $arr = [
            'custom_fields_values'  => array(),
        ];
        foreach (self::$amoFields['custom_fields_values'] as $amoFieldName => $amoFieldID){
            if (isset($billDB[$amoFieldName])){
                $locArr = array(
                    'field_id'  => $amoFieldID,
                    'values'    => $amoFieldName !== 'offers' ?
                        [['value' => $billDB[$amoFieldName]]] :
                        self::modifyOffers($billDB[$amoFieldName])
                );

                $arr['custom_fields_values'][]  = $locArr;
            }
        }

        if ($billStatus === 1){
            $arr['custom_fields_values'][] = array(
                'field_id'  => 1550056,
                'values'    => [
                    'value' => time(),
                ]
            );
        }
        return $arr;
    }

    private static function modifyOffers($offers) : array{
        $offersArr = [];
        foreach ($offers['offerNames'] as $k => $offerPrice){
            $offersArr[] = [
                'value' => [
                    'description'   => $offerPrice,
                    'unit_price'   => $offers['offerPrices'][$k],
                    'quantity'   => 1,
                ]
            ];
        }
        return $offersArr;
    }
}
