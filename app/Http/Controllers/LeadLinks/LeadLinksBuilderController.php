<?php

namespace App\Http\Controllers\LeadLinks;

use App\Http\Controllers\BuilderEntityController;
use App\Models\AmoCrmLead;
use App\Models\PLANNING;
use Illuminate\Support\Facades\DB;

class LeadLinksBuilderController extends BuilderEntityController
{
    /**
     * @param int $id
     * @return array
     * Description: get Lead row from the DB
     */
    public static function getRow(int $id) : array{
        return AmoCrmLead::find($id)->toArray() ?? [];
    }
}
