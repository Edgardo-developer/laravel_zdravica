<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\BuilderEntityController;
use App\Models\AmocrmIDs;

class LeadLinksBuilderController extends BuilderEntityController
{
    /**
     * @param int $amoLeadID
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $amoLeadID) : array{
        return AmocrmIDs::all()->where('amoLeadID', '=', $amoLeadID)->first()?->amoBillID ?? [];
    }
}
