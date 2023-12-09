<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contacts\BuilderController;
use App\Http\Controllers\Contacts\ContactsBuilderController;
use App\Http\Controllers\Leads\LeadBuilderController;
use App\Models\AmoCRMData;
use App\Models\AmoCRMLead;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

class SendToAmoCRM extends Controller
{

    /**
     * @param $DBleadId
     * @return void
     * Description: The main method, that manage the requests and main stack
     * @throws \JsonException
     */
    public function sendDealToAmoCRM($DBleadId) : void{
        $buildLead = LeadBuilderController::getRow($DBleadId);
        $buildContact = ContactsBuilderController::getRow($buildLead['patID']);
        if ($buildLead && $buildContact){
            $leadRaw=AmoCRMLead::find($DBleadId);
            $PrepareEntityController = new PrepareEntityController();
            $PresendEntityController = new PresendEntityController();
            $client = new Client(['verify' => false]);
            $contactPrepared = $PrepareEntityController->prepareContact($buildContact);

            if (!$buildLead['amoContactID']){
                $contactAmoId = $PresendEntityController->getTheContactID($client, $buildContact, $contactPrepared);
                $leadRaw->update(['amoContactID'  => $contactAmoId]);
            }else{
                $contactAmoId = $buildLead['amoContactID'];
            }

            $leadPrepared = $PrepareEntityController->prepareLead($buildLead, $contactAmoId);
            if (!$buildLead['amoLeadID']){
                $AmoLeadId = $PresendEntityController->getTheLeadID($client, $buildLead);
                $leadRaw->update(['amoLeadID'  => $AmoLeadId]);
            }else{
                $AmoLeadId = $buildLead['amoLeadID'];
            }
            $this->sendLead($client, $AmoLeadId, $leadPrepared);

            // Эти поля необходимо добавить в JOIN
            //•	Дата визита (дата и время)
            //•	Визит не состоялся (чекбокс/флаг)

            // responsibleFIO - необходимо добавить в карточку сделки
            // С помощью responsible_user_id достаем:
            // ID этого пользователя в AmoCRM
            // FIO этого пользователя в СУБД

            // Необходимо обновлять контакт при отправке Request на его проверку

            // триггеры
        }
    }

    /**
     * @param $client
     * @param $AmoLeadId
     * @param $leadPrepared
     * @return void
     */
    private function sendLead($client, $AmoLeadId, $leadPrepared) : void{
        $getRequestExt = self::getRequestExt(false);
        $headers = $getRequestExt['headers'];
        $body = json_encode($leadPrepared, JSON_THROW_ON_ERROR);
        $isUpdate = $AmoLeadId > 0;
        $method = $isUpdate ? 'PATCH': 'POST';
        $uri =  'https://zdravitsa.amocrm.ru/api/v4/leads/' . ($isUpdate ? $AmoLeadId : '');
        $request = new Request($method, $uri, $headers, $body);
        $res = $client->sendAsync($request)->wait();

        if ($res->getStatusCode() === 401){
            SendToAmoCRM::updateAccess($client);
            $this->sendLead($client, $AmoLeadId, $leadPrepared);
        }

        try {
            $result = json_decode($res->getBody(), true, 512, JSON_THROW_ON_ERROR);
            dd($result);
        }catch (\JsonException $e){
            Log::log(1, $e);
            die();
        }
    }

}
