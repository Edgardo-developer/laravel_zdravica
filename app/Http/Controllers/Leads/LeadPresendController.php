<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadPresendController extends Controller
{
    public function getAmoID(Client $client, array $DBLead): string
    {
        $leadID = $this->checkExists($client, $DBLead);
        if (!$leadID) {
            $leadID = $this->checkExistsNerazobrannoe($client,$DBLead);
        }
        return $leadID ?: 0;
    }

    private function checkExists(Client $client, array $DBLead)
    {
        $query = '?filter[statuses][0][pipeline_id]=7332486&filter[statuses][0][status_id]=61034282&query=' . $DBLead['amoContactID'];
        return $this->checkExistsLogic($client, $query);
    }

    private function checkExistsNerazobrannoe(Client $client, array $DBLead)
    {
        $query = '?filter[statuses][0][pipeline_id]=7332486&filter[statuses][0][status_id]=61034278&query=' . $DBLead['amoContactID'];
        return $this->checkExistsLogic($client, $query);
    }

    private function checkExistsLogic(Client $client, string $query){
        $res = LeadRequestController::get($client, $query);
        if ($res && $res->getStatusCode() === 200) {
            try {
                $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded']) {
                    foreach ($result['_embedded']['leads'] as $lead) {
                        if ((time() - $lead['created_at'] > 0 && time() - $lead['created_at'] < env('ZDR_LEAD_TIMEOUT'))
                            && !$lead['custom_fields_values']) {
                            return $lead['id'];
                        }
                    }
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
