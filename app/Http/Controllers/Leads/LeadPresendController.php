<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadPresendController extends Controller
{
    public function getAmoID(Client $client, array $DBLead): int
    {
        $leadID = $this->checkExists($client, $DBLead,61034278);
        if (!$leadID) {
            $leadID = $this->checkExists($client,$DBLead,61034282);
        }
        return $leadID ?: 0;
    }

    private function checkExists(Client $client, array $DBLead,int $statusID)
    {
        $query = '?filter[statuses][0][pipeline_id]=7332486&filter[statuses][0][status_id]='.$statusID.'&query=' . $DBLead['amoContactID'];
        return $this->checkExistsLogic($client, $query);
    }

    private function checkExistsLogic(Client $client, string $query){
        $res = LeadRequestController::get($client, $query.'&order[created_at]=desc');
        if ($res && $res->getStatusCode() === 200) {
            try {
                $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded'] && $result['_embedded']['leads']) {
//                    foreach ($result['_embedded']['leads'] as $lead) {
//                        if ((time() - $lead['created_at'] > 0 && time() - $lead['created_at'] < env('ZDR_LEAD_TIMEOUT'))
//                            && !$lead['custom_fields_values']) {
                            return $result['_embedded']['leads'][0]['id'];
//                        }
//                    }
                }
            } catch (JsonException $ex) {
                Log::warning($ex->getMessage());
                Log::warning($ex->getTraceAsString());
                Log::warning($ex->getLine());
            }
        }
        return 0;
    }
}
