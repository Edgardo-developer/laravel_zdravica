<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\amocrmIDs;
use GuzzleHttp\Client;

class SendToAmoCRM extends Controller
{

    /**
     * @param $DBlead
     * @return void Description: The main method, that manage the requests and main stack
     * Description: The main method, that manage the requests and main stack
     */
    public function sendDealToAmoCRM($DBlead) : void{
        $buildLead = $this->checkAmo($DBlead);
        $buildContact = ContactsBuilderController::getRow((int)$buildLead['patID'], (int)$buildLead['declareCall'] === 1);
        if ($buildLead && $buildContact){
            $buildContact['MOBIL_NYY'] = '8'.$buildContact['MOBIL_NYY'];
            $client = new Client(['verify' => false]);

            if (!isset($buildLead['amoContactID']) || $buildLead['amoContactID'] === 'null'){
                $PresendContact = new ContactsPresendController();
                $contactAmoId = $PresendContact->getAmoID($client, $buildContact);
                $buildLead['amoContactID'] = $contactAmoId;
            }else{
                $contactAmoId = $buildLead['amoContactID'];
            }

            if (!isset($buildLead['amoLeadID']) || $buildLead['amoLeadID'] === 'null'){
                $buildLead['amoContactID'] = $contactAmoId;
                $PresendLead = new LeadPresendController();
                $AmoLeadId = $PresendLead->getAmoID($client, $buildLead);
                $buildLead['amoLeadID'] = $AmoLeadId;
            }
            $leadPrepared = LeadPrepareController::prepare($buildLead, $contactAmoId);
            $leadPrepared['id'] = (integer)$buildLead['amoLeadID'];
            $this->sendLead($client, $leadPrepared);
            $arr = [];
            foreach ($buildLead as $buildLeadKey => $buildLeadValue){
                if (in_array($buildLeadKey, array('amoContactID', 'amoLeadID'))){
                    $arr[$buildLeadKey] = $buildLeadValue;
                }
            }
            $arr['leadDBId'] = $buildLead['leadDBId'];
            amocrmIDs::create($arr);
        }
    }

    /**
     * @param $client
     * @param $leadPrepared
     * @return bool
     */
    private function sendLead($client, $leadPrepared) : bool{
            LeadRequestController::update($client, [$leadPrepared]);
        return true;
    }

    private function checkAmo(array &$dbLead){
        $raw = amocrmIDs::all()->where('leadDBId', '=', $dbLead['leadDBId'])?->first();
        if ($raw){
            $rawArray = $raw->toArray();
            if ($raw['amoContactID']){
                $dbLead['amoContactID'] = $rawArray['amoContactID'];
            }
            if ($raw['amoLeadID']){
                $dbLead['amoLeadID'] = $rawArray['amoLeadID'];
            }
        }
        return $dbLead;
    }

    public function closeLead($amoLeadID){
        $leadArray = [
            'id' => (integer)$amoLeadID,
            "name" => "1",
            "closed_at"=> time() + 5,
            "status_id"=> 143,
            "updated_by"=> 0
        ];
        return $leadArray;
    }

    public function finishLead($amoLeadID){
        $leadArray = [
            'id' => (integer)$amoLeadID,
            "status_id"=> 142,
        ];
        return $leadArray;
    }
}
