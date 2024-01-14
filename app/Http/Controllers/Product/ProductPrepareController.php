<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\PrepareEntityController;

class ProductPrepareController extends PrepareEntityController
{
    public static function prepare(array $offers, int $offerSum): array
    {
        $products = [];
        foreach ($offers as $offer) {
            $products[] = [
                'name' => $offer,
                'custom_fields_values' => [
                    [
                        'field_id' => 1550012,
                        'values' => [['value' => 'Все товары']]
                    ]
                ]
            ];
        }
        return $products;
    }
}