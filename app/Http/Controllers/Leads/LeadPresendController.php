<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LeadPresendController extends Controller
{
    public function getAmoID($client, $DBLead, $leadRaw) : void{
        $leadID = $this->checkExists($client, $DBLead);
        if (!$leadID){
            LeadRequestController::create($client, LeadPrepareController::prepare($DBLead, $DBLead['amoContactID']), $leadRaw);
        }
    }

    private function checkExists($client, $DBLead){
        $query = '?query='.$DBLead['amoContactID'];
        $res = LeadRequestController::get($client, $query);
        if ($res && $res->getStatusCode() === 200){
            try {
                $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded']){
                    foreach ($result['_embedded']['leads'] as $lead){
                        if (!$lead['custom_fields_values']){
                            return $lead['id'];
                        }
                    }
                }
            }catch (\JsonException $exception){
                Log::debug($exception);
            }
        }
        return 0;
    }

//    private function createAmo($client, $contactPrepared, $leadRaw) : void{
//        LeadRequestController::create($client, $contactPrepared, $leadRaw);
//    }
}
