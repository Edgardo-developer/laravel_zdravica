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
     * @return void
     * @throws \JsonException
     */
    public function sendDealToAmoCRM($DBlead) : void{
        $buildLead = $this->checkAmo($DBlead);
        $buildContact = ContactsBuilderController::getRow((int)$buildLead['patID'],
            (int)$buildLead['declareCall'] === 1);
        if ($buildLead && $buildContact){
            $buildContact['MOBIL_NYY'] = '8'.$buildContact['MOBIL_NYY'];
            $client = new Client(['verify' => false]);

            $buildLead['amoContactID'] = $this->getContactAmoID($client, $buildLead, $buildContact);
            $buildLead['amoLeadID'] = $this->getLeadAmoID($client, $buildLead);
            $buildLead['amoBillID'] = $this->getBillAmoID($client, $buildLead);

            $leadPrepared = LeadPrepareController::prepare($buildLead, $buildLead['amoContactID']);
            $leadPrepared['id'] = (integer)$buildLead['amoLeadID'];
            $leadPrepared['pipeline_id'] = 7332486;
            $leadPrepared['status_id'] = 6103428;
            LeadRequestController::update($client, [$leadPrepared]);

            $amoData  = ['amoContactID'=>'','amoLeadID'=>'','amoBillID'=>'','offers'=>'','leadDBId'=>''];
            foreach ($amoData as $k => &$IdsName){
                if ($buildLead[$k] && $buildLead[$k] !== 'null'){
                    $amoData[$k] = $buildLead[$k];
                }else{
                    unset($amoData[$k]);
                }
            }
            unset($IdsName);
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
                || $buildLead['amoBillID'] === 'null') && (int)$buildLead['billSum'] > 0
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
        return $buildLead['amoContactID'] ?? (new ContactsPresendController())->getAmoID($client, $buildContact);
    }

    private function checkAmo(array &$dbLead) : array{
        $raw = AmocrmIDs::all()->where('leadDBId', '=', $dbLead['leadDBId'])?->first();
        $keysToCopy = ['amoContactID', 'amoLeadID', 'amoBillID','amoOffers'];
        $rawArray = $raw ? $raw->toArray() : [Null,Null,Null,Null];

        foreach ($keysToCopy as $key) {
            $dbLead[$key] = isset($raw[$key]) ? $rawArray[$key] : Null;
        }
        ksort($dbLead, SORT_NATURAL);
        return $dbLead;
    }

    public function closeLead($amoLeadID){
        return [
            'id' => (integer)$amoLeadID,
            "name" => "1",
            "closed_at"=> time() + 5,
            "status_id"=> 143,
            "updated_by"=> 0
        ];
    }

    public function finishLead($amoLeadID){
        return [
            'id' => (integer)$amoLeadID,
            "status_id"=> 142,
        ];
    }
}
