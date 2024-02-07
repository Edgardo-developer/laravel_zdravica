<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;

class LeadBuilderController extends Controller
{
    public function closeLead(int $amoLeadID): array
    {
        return [
            'id' => $amoLeadID,
            'amoLeadID' => $amoLeadID,
            'closed_at' => time() + 5,
            'status_id' => 143,
            'updated_by' => 0
        ];
    }

    public function finishLead(int $amoLeadID): array
    {
        return [
            'id' => $amoLeadID,
            'status_id' => 142,
        ];
    }
}
