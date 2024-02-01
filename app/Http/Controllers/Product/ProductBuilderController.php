<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;

class ProductBuilderController extends Controller
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
