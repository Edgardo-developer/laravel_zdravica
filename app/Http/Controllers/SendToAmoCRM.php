<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use GuzzleHttp\Client;

class SendToAmoCRM extends Controller
{

    /**
     * @param $DBleadId
     * @return bool
     * Description: The main method, that manage the requests and main stack
     * @throws \JsonException
     */
    public function sendDealToAmoCRM($DBlead) : bool{
        $buildLead = $DBlead;
        $buildContact = ContactsBuilderController::getRow((int)$buildLead['patID']);
        if ($buildLead && $buildContact){
            $buildContact['MOBIL_NYY'] = '8'.$buildContact['MOBIL_NYY'];
            $client = new Client(['verify' => false]);

            if (!$buildLead['amoContactID'] || $buildLead['amoContactID'] === 'null'){
                $PresendContact = new ContactsPresendController();
                $contactAmoId = $PresendContact->getAmoID($client, $buildContact);
                $buildLead['amoContactID'] = $contactAmoId;
            }else{
                $contactAmoId = $buildLead['amoContactID'];
            }

            if (!$buildLead['amoLeadID'] || $buildLead['amoLeadID'] === 'null'){
                $buildLead['amoContactID'] = $contactAmoId;
                $PresendLead = new LeadPresendController();
                $AmoLeadId = $PresendLead->getAmoID($client, $buildLead);
                $buildLead['amoLeadID'] = $AmoLeadId;
            }
            $leadPrepared = LeadPrepareController::prepare($buildLead, $contactAmoId);
            $leadPrepared['id'] = (integer)$buildLead['amoLeadID'];
            $this->sendLead($client, $leadPrepared);
            $string = '';
            foreach ($buildLead as $buildLeadKey => $buildLeadValue){
                if (in_array($buildLeadKey, array('amoContactID', 'amoLeadID'))){
                    $string .= $buildLeadValue;
                    if ($buildLeadKey === 'amoLeadID'){
                        $string .= ',';
                    }
                }
            }
            echo $string;
            return false;
        }
        return false;
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
