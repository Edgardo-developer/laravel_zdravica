<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Bill\BillPresendController;
use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\LeadLinks\LeadLinksPrepareController;
use App\Http\Controllers\LeadLinks\LeadLinksRequestController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmocrmIDs;
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

            $buildLead['amoContactID'] = $this->getContactAmoID($client, $buildLead, $buildContact);
            $buildLead['amoLeadID'] = $this->getLeadAmoID($client, $buildLead);
            $buildLead['amoBillID'] = $this->getBillAmoID($client, $buildLead);

            $leadPrepared = LeadPrepareController::prepare($buildLead, $buildLead['amoContactID']);
            $leadPrepared['id'] = (integer)$buildLead['amoLeadID'];
            LeadRequestController::update($client, [$leadPrepared]);

            $amoData = array(
                'amoContactID'  => $buildLead['amoContactID'] ?? NULL,
                'amoLeadID' => $buildLead['amoLeadID'] ?? NULL,
                'amoBillID' => $buildLead['amoBillID'] ?? NULL,
                'amoOffers' => $buildLead['offers'] ?? NULL,
                'leadDBId' => $buildLead['leadDBId'] ?? NULL
            );
            AmocrmIDs::updateOrCreate([
                'leadDBId' => $buildLead['leadDBId']
            ],$amoData);
        }
    }

    /**
     * @param $client
     * @param $buildLead
     * @return int
     */
    private function getLeadAmoID($client, $buildLead) : int{
        if (!isset($buildLead['amoLeadID']) || $buildLead['amoLeadID'] === 'null'){
            return (new LeadPresendController())->getAmoID($client, $buildLead);
        }
        return $buildLead['amoLeadID'];
    }

    /**
     * @param $client
     * @param $buildLead
     * @return int
     * @throws \JsonException
     */
    private function getBillAmoID($client, $buildLead) : int{
        $billDB = array(
            'offers'    => $buildLead['offers'],
            'billStatus'    => 0,
            'status'    =>  'Создан',
            'account'   => array(
                "entity_type" => "contacts",
                "entity_id"=> $buildLead['amoContactID'],
            )
        );

        if ((!isset($buildLead['amoBillID'], $buildLead['offers'], $buildLead['price'])
                || $buildLead['amoBillID'] === 'null') &&
            (int)$buildLead['price'] > 0
        ){
            $PresendBill = new BillPresendController();
            $AmoBillID = $PresendBill->getAmoID($client, $billDB);

            $leadLinks = LeadLinksPrepareController::prepare($buildLead, $buildLead['amoContactID']);
            $leadLinks['amoLeadID'] = $buildLead['amoLeadID'];
            LeadLinksRequestController::create($client, $leadLinks);
            return $AmoBillID;
        }

        if($buildLead['amoOffers'] && $buildLead['offers'] !== $buildLead['amoOffers']){
            $PresendBill = new BillPresendController();
            $PresendBill->updateBill($client, $billDB);
        }
        return $buildLead['amoBillID'] ?? 0;
    }

    /**
     * @param $client
     * @param $buildLead
     * @param $buildContact
     * @return int
     */
    private function getContactAmoID($client, $buildLead, $buildContact) : int{
        if (!isset($buildLead['amoContactID']) || $buildLead['amoContactID'] === 'null'){
            return (new ContactsPresendController())->getAmoID($client, $buildContact);
        }
        return $buildLead['amoContactID'];
    }

    private function checkAmo(array &$dbLead){
        $raw = AmocrmIDs::all()->where('leadDBId', '=', $dbLead['leadDBId'])?->first();
        if ($raw){
            $rawArray = $raw->toArray();
            $keysToCopy = ['amoContactID', 'amoLeadID', 'amoBillID','amoOffers'];

            foreach ($keysToCopy as $key) {
                if (isset($raw[$key])) {
                    $dbLead[$key] = $rawArray[$key];
                }else{
                    $dbLead[$key] = Null;
                }
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
