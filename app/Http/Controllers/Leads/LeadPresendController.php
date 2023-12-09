<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\PresendEntityController;
use Illuminate\Support\Facades\Log;

class LeadPresendController extends PresendEntityController
{

    public function getAmoID($client, $DBLead, $contactPrepared = []) : int{
        $contactID = $this->checkExists($client, $DBLead);
        if (!$contactID){
            $contactPrepared = LeadPrepareController::prepare($DBLead, $DBLead['amoContactID']);
            $contactID = $this->createAmo($client, $contactPrepared);
        }
        return $contactID;

    }

    private function checkExists($client, $DBLead){
        $query = '?query='.$DBLead['amoContactID'];
        $res = LeadRequestController::get($client, $query);
        try {
            $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
        }catch (\JsonException $exception){
            Log::debug($exception);
        }
        if (isset($result) && $result['_embedded']){
            foreach ($result['_embedded']['leads'] as $lead){
                if (!$lead['custom_fields_values']){
                    return $lead['id'];
                }
            }
        }
        return 0;
    }

    private function createAmo($client, $contactPrepared) : int{
        return 0;
    }
}
