<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillPresendController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function getAmoID($client, $billDB) : string{
        return BillRequestController::create($client, BillPrepareController::prepare($billDB, $billDB['billStatus']));
    }

    public function updateBill($client, $billDB) : void{
        BillRequestController::update($client, BillPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
