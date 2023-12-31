<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PrepareEntityController;
use Illuminate\Http\Request;

class BillPrepareController extends PrepareEntityController
{
    private static array $amoFields = [
        //'name',
        'custom_fields_values'  =>  [
            1550048  => "status",
            1550052  => "account",
            1550058  => "offers",
//            1550056  => "bought_date",
        ],
    ];

    public static function prepare(array $billDB, int $billStatus) : array{
        $arr = [
            'custom_fields_values'  => array(),
        ];
        foreach (self::$amoFields['custom_fields_values'] as $amoFieldKey => $amoFieldVal){
            if (isset($billDB[$amoFieldVal])){
                $locArr = array(
                    'field_id'  => $amoFieldKey,
                    'values'    => [
                        'value' => $billDB[$amoFieldVal],
                    ]
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
}
