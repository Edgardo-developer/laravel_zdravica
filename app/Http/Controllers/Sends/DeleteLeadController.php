<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillBuilderController;
use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Bill\BillRequestController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class DeleteLeadController extends Controller
{
    public function __construct($ids){
        $client = new Client(['verify'=>false]);
        $this->BillController = new BillController($client);
        $this->LeadController = new LeadController($client);
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
                    $this->LeadController->closeLead($leadID) :
                    $this->LeadController->finishLead($leadID);
            }
            if ($billID > 0) {
                $billArray[] = $this->BillController->prepare(
                    $billID, $withReason ? 0 : 1);
            }
        }
        $this->removeThem($leadArray,$billArray);
        return true;
    }

    private function removeThem($leadArray, $billArray){
        if (count($leadArray[0]) > 0) {
            if (count($billArray) > 0) {
                $this->BillController->updateBill($billArray, $billArray['billStatus']);
            }
            $leadArray['delete'] = true;
            $this->LeadController->update($leadArray);
        }
    }
}
