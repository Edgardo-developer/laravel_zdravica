<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\AmoProducts;
use App\Models\OffersDB;

class ProductPresendController extends Controller
{
    public function __construct()
    {

    }

    public function saveToDB($undefinedAmo, $newIds) : void{
        $rows = [];
        foreach ($newIds as $k => $newId){
            $first = amoProducts::all('FM_SERV_ID')->where('FM_SERV_NAME', '=', $undefinedAmo[$k])->first();
            if ($first){
                $rows[] = [
                    'name'  => $undefinedAmo[$k],
                    'amoID' => $newId,
                    'DBId'  => $first->FM_SERV_ID,
                ];
            }
        }
        AmoProducts::create($rows);
    }
}
