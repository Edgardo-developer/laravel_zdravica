<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class DeleteLeadController extends Controller
{
    public function __construct($ids){
        $this->dbIDs = $ids;
    }

    public function deleteLeads(bool $withReason){
        $leadArray = [];
        $billArray = [];
        foreach ($this->dbIDs as $dbID) {
            $leadID = (int)$dbID;
            $billID = amocrmIDs::where('amoLeadID', '=', $dbID)->first()->amoBillID;

            if ($leadID > 0) {
                $leadArray[] = $withReason ?
                    LeadBuilderController::closeLead($leadID) :
                    LeadBuilderController::finishLead($leadID);
            }
            if ($billID > 0 && $withReason) {
                $billArray[] = BillBuilderController::finishBill($billID);
            }
        }
        $this->removeThem($leadArray,$billArray);
        return true;
    }

    private function removeThem($leadArray, $billArray){
        $client = new Client(['verify' => false]);
        if (count($leadArray[0]) > 0) {
            if (count($billArray) > 0) {
                BillRequestController::update($client, $billArray);
            }
            LeadRequestController::update($client, $leadArray);
        }
    }
}
