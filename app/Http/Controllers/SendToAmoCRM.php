<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Bill\BillPresendController;
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

            if ((!isset($buildLead['amoBillID'], $buildLead['offers'], $buildLead['price'])
                    || $buildLead['amoBillID'] === 'null') &&
                (int)$buildLead['price'] > 0
            ){
                $billDB = array(
                    'offers'    => $buildLead['offers'],
                    'billStatus'    => 0,
                    'status'    =>  'Создан',
                    'account'   => array(
                        "entity_type" => "contacts",
                        "entity_id"=> $buildLead['amoContactID'],
                    )
                );
                $PresendBill = new BillPresendController();
                $AmoBillID = $PresendBill->getAmoID($client, $billDB);
                $buildLead['amoBillID'] = $AmoBillID;
            }

            $leadPrepared = LeadPrepareController::prepare($buildLead, $contactAmoId);
            $leadPrepared['id'] = (integer)$buildLead['amoLeadID'];
            $this->sendLead($client, $leadPrepared);
            $amoData = array(
                'amoContactID'  => $buildLead['amoContactID'] ?? '',
                'amoLeadID' => $buildLead['amoLeadID'] ?? '',
                'amoBillID' => $buildLead['amoBillID'] ?? '',
                'leadDBId' => $buildLead['amoBillID'] ?? ''
            );

            amocrmIDs::updateOrCreate([
                'leadDBId' => $buildLead['leadDBId']
            ],$amoData);
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
            if ($raw['amoBillID']){
                $dbLead['amoBillID'] = $rawArray['amoBillID'];
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
