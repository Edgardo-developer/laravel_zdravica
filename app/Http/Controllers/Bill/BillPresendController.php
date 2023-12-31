<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Bill\BillPrepareController;
use App\Http\Controllers\Bill\BillRequestController;
use Illuminate\Http\Request;

class BillPresendController extends Controller
{
    public function getAmoID($client, $billDB) : string{
//        $leadID = $this->checkExists($client, $DBLead);
//        if (!$leadID){
//            $leadID = LeadRequestController::create($client, LeadPrepareController::prepare($DBLead, $DBLead['amoContactID']));
//        }
        return BillRequestController::create($client, BillPrepareController::prepare($billDB, $billDB['billStatus']));
    }
}
