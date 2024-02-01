<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\Controller;
use App\Models\AmocrmIDs;

class LeadLinksBuilderController extends Controller
{
    /**
     * @param int $amoLeadID
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $amoLeadID): array
    {
        return AmocrmIDs::where('amoLeadID', '=', $amoLeadID)->first()?->amoBillID ?? [];
    }
}
