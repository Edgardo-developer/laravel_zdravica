<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillPresendController extends Controller
{
    public function getAmoID($client, $billDB): string
    {
        $preparedData = BillPrepareController::prepare($billDB, $billDB['billStatus']);
        return BillRequestController::create($client, $preparedData);
    }

    public function updateBill($client, $billDB): void
    {
        BillRequestController::update($client, BillPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
