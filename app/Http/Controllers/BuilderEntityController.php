<?php

namespace App\Http\Controllers;

use App\Models\PATIENTS;
use Illuminate\Support\Facades\DB;

class BuilderEntityController extends Controller
{
    public function buildEntity($leadID){
        $lead = $this->getLead($leadID);
        return [
            'contact'   => $lead ? $this->getContactArray($lead['contactId'], $lead['declareVisit']) : '', // Нужна проверка на первое посещение
            'lead'      => $lead ?: '',
        ];
    }

    private function getLead($leadID) : array{
        $lead = DB::query()->where('ID', $leadID)->first();
        if ($lead){
            return $lead->toArray();
        }
        return [];
    }

    private function getContactArray($contactId, $declareVisit = false) : array{
        return PATIENTS::all()
            ->where('id', '=', $contactId)
            ->first($this->getColumns($declareVisit))->toArray();
    }

    private function getColumns($declareVisit) : array{
        $columns = [
            'id', // ID
            'NOM', 'PRENOM', 'PATRONYME', // FIO
            'MOBIL_NYY', // Mobile
            'EMAIL', // Email
            'POL', // POL
            'GOROD', // CITY
            'NE_LE', //  DATE OF BIRTH
        ];
        if ($declareVisit){
            $columns = array_merge($columns, [
                'RAYON_VYBORKA', // District
                'ULICA', // Street
                'DOM', // DOM
                'KVARTIRA', // FLAT
            ]);
        }
        return $columns;
    }
}
