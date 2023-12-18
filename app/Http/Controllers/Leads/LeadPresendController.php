<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class LeadPresendController extends Controller
{
    public function getAmoID($client, $DBLead) : string{
        $leadID = $this->checkExists($client, $DBLead);
        if (!$leadID){
            $leadID = LeadRequestController::create($client, LeadPrepareController::prepare($DBLead, $DBLead['amoContactID']));
        }
        return $leadID;
    }

    private function checkExists($client, $DBLead){
        $query = '?query='.$DBLead['amoContactID'];
        $res = LeadRequestController::get($client, $query);
        if ($res && $res->getStatusCode() === 200){
            try {
                $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded']){
                    $a = 0;
                    foreach ($result['_embedded']['leads'] as $lead){
                        if ($lead['created_at'] > $a){
                            $a = $lead['created_at'];
                        }
                        if (time() - $lead['created_at'] > 0 && time() - $lead['created_at'] < 120){
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
