<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\PresendEntityController;
use Illuminate\Support\Facades\Log;

class LeadPresendController extends PresendEntityController
{

    public function getAmoID($client, $DBLead, $leadPrepared = []) : int{
        $leadID = $this->checkExists($client, $DBLead);
        if (!$leadID){
            $leadPrepared = LeadPrepareController::prepare($DBLead, $DBLead['amoContactID']);
            $leadID = $this->createAmo($client, $leadPrepared);
        }
        return $leadID;
    }

    private function checkExists($client, $DBLead){
        $query = '?query='.$DBLead['amoContactID'];
        $res = LeadRequestController::get($client, $query);
        if(!$res){
            return null;
        }
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
        $res = LeadRequestController::create($client, $contactPrepared);
        dd($res);
        try {
            $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
            if ($result && $result['_embedded']){
                return $result['_embedded']['leads'][0]['id'];
            }
        }catch (\JsonException $exception){
            Log::debug($exception);
        }
        return 0;
    }
}
