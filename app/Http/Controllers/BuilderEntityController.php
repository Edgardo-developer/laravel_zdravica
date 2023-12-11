<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contacts\BuilderController;
use App\Models\AmoCrmLead;

class BuilderEntityController extends Controller
{
    /**
     * @param int $leadID
     * @return array
     * Description: return the DB rows of contact and lead
     */
    public function buildEntity(int $leadID)
    {
//        $lead = BuilderLeadController::getLead($leadID);
        $lead = AmoCrmLead::all()->where('id', '=', $leadID)->first()->toArray();
        return [
            'contact' => $lead ? BuilderController::getRow($lead['patID'], $lead['declareVisit']) : '',
            // Нужна проверка на первое посещение
            'lead' => $lead ?: '',
        ];
    }

    public static function getRow(int $id) : array{
        return [];
    }
    protected static function getColumns(int $id) : array{
        return [];
    }
}
