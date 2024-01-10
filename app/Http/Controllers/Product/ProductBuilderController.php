<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\BuilderEntityController;
use App\Models\OffersDB;

class ProductBuilderController extends BuilderEntityController
{
    public static function getRow(string|int $offerName): array
    {
        if (is_string($offerName)) {
            $offerRaw = OffersDB::all('amoID')->where('name', '=', $offerName)->first();
            if ($offerRaw) {
                return $offerRaw->toArray();
            }
        }
        return ['amoID' => 0];
    }
}
