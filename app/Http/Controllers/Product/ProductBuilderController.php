<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\BuilderEntityController;
use App\Models\AmoProducts;

class ProductBuilderController extends BuilderEntityController
{
    public static function getRow(string|int $offerName): array
    {
        if (is_string($offerName)) {
            $offerRaw = amoProducts::where('name', '=', $offerName);
            if ($offerRaw->first()) {
                return ['amoID' => $offerRaw->first()->amoID];
            }
        }
        return ['amoID' => 0];
    }
}
