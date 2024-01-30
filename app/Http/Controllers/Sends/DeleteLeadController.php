<?php

namespace App\Http\Controllers\Sends;

use App\Http\Controllers\Leads\LeadRequestController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmocrmIDs;
use GuzzleHttp\Client;

class DeleteLeadController extends SendToAmoCRM
{
    public function __construct(array $ids){
        $this->ids = $ids;
    }

    public function deleteLeads(){
        $ids = $this->ids;
        $arr = $this->declareRequest($ids);
        if ($arr){
            $client = new Client(['verify'=>false]);
            LeadRequestController::update($client, $arr);
        }
        AmocrmIDs::where('leadDBId', $ids)->delete();
    }

    private function declareRequest(array $ids){
        $leadArr = [];
        foreach ($ids as $id){
            $leadArr[] = [
                "id"    => $id,
                "status_id" => 143,
                "pipeline_id" => 7332486
            ];
        }
        return $leadArr;
    }
}
