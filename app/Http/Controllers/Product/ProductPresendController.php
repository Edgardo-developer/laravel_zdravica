<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;
use App\Models\OffersDB;

class ProductPresendController extends Controller
{
    public function getAmoIDs($client, $amoProductNames): array
    {
        $ids = [];
        $undefinedAmo = [];
        foreach ($amoProductNames as $amoProductName) {
            $amoID = ProductBuilderController::getRow($amoProductName);
            if ($amoID['amoID'] === 0) {
                $undefinedAmo[] = $amoProductName;
            } else {
                $ids[] = $amoID['amoID'];
            }
        }

        if (count($undefinedAmo) > 0) {
            $prepared = ProductPrepareController::prepare($undefinedAmo, 1);
            $newIds = ProductRequestController::create($client, $prepared);
            self::saveToDB($undefinedAmo, $newIds);
            $ids = array_merge($ids, $newIds);
        }
        return $ids;
    }

    private static function saveToDB($undefinedAmo, $newIds) : void{
        $rows = [];
        foreach ($newIds as $k => $newId){
            $first = amoProducts::all('FM_SERV_ID')->where('FM_SERV_NAME', '=', $undefinedAmo[$k])->first();
            if ($first){
                $rows[] = [
                    'name'  => $undefinedAmo[$k],
                    'amoID' => $newId,
//                    'DBId'  => $first->FM_SERV_ID,
                    'DBId'  => random_int(1,10),
                ];
            }
        }
        AmoProducts::insert($rows);
    }
}
