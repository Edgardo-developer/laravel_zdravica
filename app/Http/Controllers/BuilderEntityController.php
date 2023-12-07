<?php

namespace App\Http\Controllers;

use App\Models\PATIENTS;
use Illuminate\Support\Facades\DB;

class BuilderEntityController extends Controller
{
    /**
     * @param int $leadID
     * @return array
     * Description: return the DB rows of contact and lead
     */
    public function buildEntity(int $leadID){
        $lead = $this->getLead($leadID);
        return [
            'contact'   => $lead ? $this->getContactRow($lead['PATIENTS_ID'], $lead['declareVisit']) : '', // Нужна проверка на первое посещение
            'lead'      => $lead ?: '',
        ];
    }

    /**
     * @param int $leadID
     * @return array
     * Description: get Lead row from the DB
     */
    private function getLead(int $leadID) : array{
        $lead = DB::query()->where('ID', $leadID)->first();
        if ($lead){
            return $lead->toArray();
        }
        return [];
    }

    /**
     * @param int $contactId
     * @param bool $declareVisit
     * @return array
     * Description: get row of the contact
     */
    private function getContactRow(int $contactId, bool $declareVisit = false) : array{
        return PATIENTS::all()
            ->where('id', '=', $contactId)
            ->first($this->getColumns($declareVisit))->toArray();
    }

    /**
     * @param bool $declareVisit
     * @return string[]
     * Description: returns columns regarding the declare visit
     */
    private function getColumns(bool $declareVisit) : array{
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
