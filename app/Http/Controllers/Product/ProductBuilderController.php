<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\BuilderEntityController;

class ProductBuilderController extends BuilderEntityController
{
    public static function closeBill($billID){
        return array(
            'id' => $billID,
            'is_deleted'    => true,
        );
    }

    public static function finishBill($billID){
        return array(
            'id' => $billID,
            'custom_fields_values'  => [
                [
                    "field_id"  =>  1550048,
                    "values"    =>  [
                        [
                            "value" =>  'Оплачен',
                        ]
                    ]
                ]
            ],
        );
    }
}
