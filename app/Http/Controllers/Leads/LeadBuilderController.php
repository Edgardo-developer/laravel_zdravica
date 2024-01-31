<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\BuilderEntityController;
use App\Models\AmoCrmLead;

class LeadBuilderController extends BuilderEntityController
{
    /**
     * @param int $id
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $id): array
    {
        return AmoCrmLead::find($id,'id')->toArray() ?? [];
    }

    public static function closeLead($amoLeadID): array
    {
        return [
            'id' => (int)$amoLeadID,
            'amoLeadID' => (int)$amoLeadID,
            'name' => '1',
            'closed_at' => time() + 5,
            'status_id' => 143,
            'updated_by' => 0
        ];
    }

    public static function finishLead($amoLeadID): array
    {
        return [
            'amoLeadID' => (int)$amoLeadID,
            'status_id' => 142,
        ];
    }
}
