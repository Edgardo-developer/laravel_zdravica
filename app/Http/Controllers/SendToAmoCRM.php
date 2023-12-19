<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Contacts\ContactsPresendController;
use App\Http\Controllers\Leads\LeadPrepareController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmoCrmLead;
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
        $buildContact = ContactsBuilderController::getRow($buildLead['patID']);
        if ($buildLead && $buildContact){
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

            // responsibleFIO - необходимо добавить в карточку сделки
            // С помощью responsible_user_id достаем:
            // ID этого пользователя в AmoCRM
            // FIO этого пользователя в СУБД
        }
        return false;
    }

    /**
     * @param $client
     * @param $leadPrepared
     * @return bool
     */
    private function sendLead($client, $leadPrepared) : bool{
//        $isUpdate = $AmoLeadId > 0;
//        if ($isUpdate){
            LeadRequestController::update($client, [$leadPrepared]);
//        }else{
//            $res = LeadRequestController::create($client, $leadPrepared);
//        }

//        if ($res){
//            try {
//                $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
//            }catch (\JsonException $e){
//                dd($e);
//            }
//        }
        return true;
    }

    public function closeLead($client, $amoLeadID){
        $leadArray = [
            'id' => (integer)$amoLeadID,
            "name" => "1",
            "closed_at"=> time() + 5,
            "status_id"=> 143,
            "updated_by"=> 0
        ];
        return $leadArray;
    }
}
