<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\BuilderEntityController;
use App\Models\PATIENTS;

class ContactsBuilderController extends BuilderEntityController
{
    public static function getRow(int $id, bool $declareCall = false) : array{
        $patient =  PATIENTS::all(self::getColumns($declareCall ? 1 : 0))
            ->where('id', '=', $id)
            ?->first()?->toArray();
        if (!is_null($patient)){
            return $patient;
        }
        return [];
    }

    /**
     * @param int $declareVisit
     * @return string[]
     * Description: returns columns regarding the declare visit
     */
    protected static function getColumns(int $declareVisit) : array{
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
