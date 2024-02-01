<?php

namespace App\Http\Controllers\Contacts;

use App\Http\Controllers\Controller;
use App\Models\PATIENTS;

class ContactsBuilderController extends Controller
{
    public static function getRow(int $id, bool $declareCall = false): array
    {
        $pat = PATIENTS::where("PATIENTS_ID",$id);
        if($pat->count() > 0){
            return $pat->first()->only(self::getColumns($declareCall ? 1 : 0));
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
