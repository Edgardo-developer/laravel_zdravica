<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\LeadController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class DeleteLeadController extends Controller
{
    private BillController $BillController;
    private LeadController $LeadController;
    private array $dbIDs;

    public function __construct($ids){
        $client = new Client(['verify'=>false]);
        $this->BillController = new BillController($client);
        $this->LeadController = new LeadController($client);
        $this->dbIDs = $ids;
    }

    public function deleteLeads(bool $withReason) : void{
        $leadArray = [];
        $billArray = [];
        foreach ($this->dbIDs as $dbID) {
            $leadID = (int)$dbID;
            $billObj = AmocrmIDs::with('WITH(NOLOCK)')->where('amoLeadID','=', $leadID)->first();

            if ($leadID > 0) {
                $leadArray[] = $withReason ?
                    $this->LeadController->closeLead($leadID) :
                    $this->LeadController->finishLead($leadID);
            }
            if ($billObj) {
                $billArray[] = $this->BillController->prepare(
                    $billObj->amoBillID, $withReason ? 0 : 1);
                $billObj->delete();
            }
        }
        $this->removeThem($leadArray,$billArray);
    }

    private function removeThem($leadArray, $billArray) : void{
        if (count($leadArray[0]) > 0) {
            if (count($billArray) > 0) {
                $this->BillController->updateBill($billArray, $billArray['billStatus']);
            }
            $leadArray['delete'] = true;
            $this->LeadController->update($leadArray);
        }
    }
}
