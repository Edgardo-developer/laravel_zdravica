<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use JsonException;

class LeadPresendController extends Controller
{
    public function getAmoID($client, $DBLead): string
    {
        $leadID = $this->checkExists($client, $DBLead);
        if (!$leadID) {
            $leadID = LeadRequestController::create(
                $client,
                LeadPrepareController::prepare($DBLead, $DBLead['amoContactID'])
            );
        }
        return $leadID;
    }

    private function checkExists($client, $DBLead)
    {
        $query = '?filter[statuses][0][pipeline_id]=7332486&filter[statuses][0][status_id]=61034282&query=' . $DBLead['amoContactID'];
        $res = LeadRequestController::get($client, $query);
        if ($res && $res->getStatusCode() === 200) {
            try {
                $result = $res->getBody() ? json_decode($res->getBody(), 'true', 512, JSON_THROW_ON_ERROR) : '';
                if (isset($result) && $result['_embedded']) {
                    foreach ($result['_embedded']['leads'] as $lead) {
                        if ((time() - $lead['created_at'] > 0 && time() - $lead['created_at'] < 180)
                            && count($lead['custom_fields_values']) === 0) {
                            return $lead['id'];
                        }
                    }
                }
            } catch (JsonException $exception) {
                Log::debug($exception);
            }
        }
        return 0;
    }
}
