<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;

class ProductPresendController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function getAmoID($client, $billDB) : string{
        $preparedData = ProductPrepareController::prepare($billDB, $billDB['billStatus']);
        return ProductRequestController::create($client, $preparedData);
    }

    public function getAmoIDs($client, $offers){
        $preparedData = ProductPrepareController::prepare($offers, 1);
    }

    public function updateBill($client, $billDB) : void{
        ProductRequestController::update($client, ProductPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
