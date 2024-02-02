<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillGeneralController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\LeadGeneralController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class DeleteLeadController extends Controller
{
    public function __construct($ids){
        $client = new Client(['verify'=>false]);
        $this->BillGeneralController = new BillGeneralController($client);
        $this->LeadGeneralController = new LeadGeneralController($client);
        $this->dbIDs = $ids;
    }

    public function deleteLeads(bool $withReason){
        $leadArray = [];
        $billArray = [];
        foreach ($this->dbIDs as $dbID) {
            $leadID = (int)$dbID;
            $billID = amocrmIDs::where('amoLeadID', $dbID)->first()->amoBillID;

            if ($leadID > 0) {
                $leadArray[] = $withReason ?
                    $this->LeadGeneralController->closeLead($leadID) :
                    $this->LeadGeneralController->finishLead($leadID);
            }
            if ($billID > 0) {
                $billArray[] = $this->BillGeneralController->prepare(
                    $billID, $withReason ? 0 : 1);
            }
        }
        $this->removeThem($leadArray,$billArray);
        return true;
    }

    private function removeThem($leadArray, $billArray){
        if (count($leadArray[0]) > 0) {
            if (count($billArray) > 0) {
                $this->BillGeneralController->updateBill($billArray);
            }
            $leadArray['delete'] = true;
            $this->LeadGeneralController->update($leadArray);
        }
    }
}
