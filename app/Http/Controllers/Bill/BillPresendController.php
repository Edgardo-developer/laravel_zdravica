<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillPresendController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function getAmoID($client, $billDB) : string{
        $preparedData = ProductPrepareController::prepare($billDB, $billDB['billStatus']);
        return ProductRequestController::create($client, $preparedData);
    }

    public function updateBill($client, $billDB) : void{
        ProductRequestController::update($client, ProductPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
