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
//        $lead = $this->getLead($leadID);
        $lead = [
            'PATIENTS_ID'   =>  1,
            'declareVisit'  => true,
            'summa' =>  350,
            'status'    => 1,
            'napravlenie'   =>  'pravoe',
            'filial'    => 'isani',
            'vrach' => 'specializacii',
            'usluga'    =>  'usluga',
            'date'  => '23.01.2001',
            'visit' => 1,
            'created_at'    => '10.07.2023',
            'updated_at'    => '12.07.2023',
        ];
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
        return PATIENTS::all($this->getColumns($declareVisit))
            ->where('id', '=', $contactId)
            ->first()->toArray();
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
            'created_at', //  DATE OF BIRTH
            'updated_at', //  DATE OF BIRTH
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
