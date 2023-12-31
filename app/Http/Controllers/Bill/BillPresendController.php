<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Bill\BillPrepareController;
use App\Http\Controllers\Bill\BillRequestController;
use Illuminate\Http\Request;

class BillPresendController extends Controller
{
    /**
     * @throws \JsonException
     */
    public function getAmoID($client, $billDB) : string{
        return BillRequestController::create($client, BillPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
