<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\BuilderEntityController;
use App\Models\PATIENTS;

class ContactsBuilderController extends BuilderEntityController
{
    public static function getRow(int $id, bool $declareCall = false): array
    {
        $patientObject = PATIENTS::find($id,'PATIENTS_ID');
        if($patientObject){
            $patient = $patientObject->only(self::getColumns($declareCall ? 1 : 0));
        }
        if (!is_null($patientObject) && isset($patient)) {
            return $patient;
        }
        return [];
    }

    /**
     * @param int $declareVisit
     * @return array
     * Description: returns columns regarding the declare visit
     */
    protected static function getColumns(int $declareVisit): array
    {
        $columns = [
            'PATIENTS_ID',
            'NOM',
            'PRENOM',
            'PATRONYME',
            'MOBIL_NYY',
            'EMAIL',
            'POL',
            'GOROD',
            'NE_LE',
            'created_at',
            'updated_at',
        ];
        if ($declareVisit) {
            $columns = array_merge($columns, [
                'RAYON_VYBORKA',
                'ULICA',
                'DOM',
                'KVARTIRA',
            ]);
        }
        return $columns;
    }
}
