<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;

class ProductPresendController extends Controller
{
    public function getAmoIDs($client, $amoProductNames): array
    {
        $ids = [];
        $undefinedAmo = [];
        foreach ($amoProductNames as $amoProductName) {
            $amoID = ProductBuilderController::getRow($amoProductName);
            if ($amoID['amoID'] === 0) {
                $undefinedAmo[] = [
                    'name' => $amoProductName
                ];
            } else {
                $ids[] = $amoID['amoID'];
            }
        }

        if (count($undefinedAmo) > 0) {
            $prepared = ProductPrepareController::prepare($undefinedAmo, 1);
            $newIds = ProductRequestController::create($client, $prepared);
            $ids = array_merge($ids, $newIds);
        }

        return $ids;
    }
}
