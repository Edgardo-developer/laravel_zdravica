<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;

class ProductPresendController extends Controller
{
    public function __construct(){}

    public function saveToDB(array $undefinedAmo, array $newIds) : void{
        $rows = [];
        foreach ($newIds as $k => $newId){
            $first = amoProducts::all('DBId')->where('DBId', '=', $undefinedAmo[$k])->first();
            if ($first){
                $rows[] = [
                    'name'  => $undefinedAmo[$k],
                    'amoID' => $newId,
                    'DBId'  => $first->DBId,
                ];
            }
        }
        AmoProducts::create($rows);
    }
}
