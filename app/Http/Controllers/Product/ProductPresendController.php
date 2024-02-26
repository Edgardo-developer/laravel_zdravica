<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;
use App\Models\OffersDB;

class ProductPresendController extends Controller
{
    public function __construct(){}

    public function saveToDB(array $undefinedAmo, array $newIds) : void{
        $rows = [];
        foreach ($newIds as $k => $newId){
            $first = OffersDB::where('LABEL', $undefinedAmo[$k])->first();
            if ($first){
                $rows[] = [
                    'name'  => $undefinedAmo[$k],
                    'amoID' => $newId,
                    'DBId'  => $first->DBId,
                    'sku'  => $first->CODE,
                ];
            }
        }
        AmoProducts::create($rows);
    }
}
