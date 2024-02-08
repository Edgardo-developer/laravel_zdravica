<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Leads\LeadController;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class DeleteLeadController extends Controller
{
    private BillController $BillController;
    private LeadController $LeadController;
    private array $amoIDs;

    public function __construct($amoIDs = []){
        $client = new Client(['verify'=>false]);
        $this->BillController = new BillController($client);
        $this->LeadController = new LeadController($client);
        $this->amoIDs = $amoIDs;
    }

    public function deleteLeads(bool $withReason) : Response|array{
        $leadArray = [];
        $billArray = [];
        foreach ($this->amoIDs as $amoID) {
            $amoID = (int)$amoID;
            $leadObj = AmocrmIDs::where('amoLeadID','=', $amoID)->first();

            if ($amoID > 0) {
                $leadArray[] = $withReason ?
                    $this->LeadController->closeLead($amoID) :
                    $this->LeadController->finishLead($amoID);
            }
            if ($leadObj && (int)$leadObj->amoBillID > 0 && $withReason) {
                $billArray[] = $this->BillController->builder($leadObj->amoBillID);
            }
            //$leadObj->delete();
        }
        return $this->removeThem($leadArray,$billArray);
    }

    private function removeThem($leadArray, $billArray) : Response|array{
        if (count($leadArray[0]) > 0) {
            if (count($billArray) > 0) {
                $this->BillController->updateBill($billArray, $billArray['billStatus']);
            }
            $leadArray['delete'] = true;
            return $this->LeadController->update($leadArray);
        }
        return [];
    }
}
