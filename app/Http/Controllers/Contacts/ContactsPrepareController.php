<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\PrepareEntityController;


class ContactsPrepareController extends PrepareEntityController
{
    // fields of Contact in the AmoCRM
    private static array $amoFields = [
        'name',
        'first_name',
        'last_name',
        'custom_fields_values'  => [
            170783 => 'mobile',
            170785 => 'email',
            391181 => 'FIO',
            391183 => 'Birthday',
            391185 => 'POL',
        ]
    ];

    /**
     * @param array $contactDB
     * @param int $contactID
     * @return array
     * Description: prepares the array for the contact
     */
    public static function prepare(array $contactDB, $contactID = 0) : array{
        $prepared = array();
        foreach (self::$amoFields as $mergedContactField){
            if (is_string($mergedContactField)){
                $prepared[$mergedContactField] = self::matchFields($mergedContactField, $contactDB);
            }else{
                foreach ($mergedContactField as $customFieldsKey => $customFieldsValue){
                    $prepared['custom_fields_values'][] = [
                        'field_id'  =>  $customFieldsKey,
                        'values'    =>  [['value'=> self::matchFields($customFieldsValue, $contactDB)]],
                    ];
                }
            }
        }
        return [$prepared];
    }

    /**
     * @param string $mergedContactField
     * @param array $contactDB
     * @return string
     * Description: sets the new values
     */
    private static function matchFields(string $mergedContactField, array $contactDB): string
    {
        return match($mergedContactField){
            'name'  => $contactDB['NOM'] . ' ' . $contactDB['PRENOM'],
            'first_name'  => $contactDB['NOM'] ?? '',
            'last_name'  => $contactDB['PRENOM'] ?? '',
            'created_by'  => $contactDB['created_at'] ?? '',
            'updated_by'  => $contactDB['updated_at'] ?? '',
            'mobile',   =>  $contactDB['MOBIL_NYY'] ?? '',
            'email',    =>  $contactDB['EMAIL'] ?? '',
            'FIO',  =>  $contactDB['NOM'] . ' ' . $contactDB['PRENOM'] . ' ' . $contactDB['PATRONYME'],
            'Birthday', =>  $contactDB['NE_LE'] ?? '',
            'POL',  =>  $contactDB['POL'] ? 'Мужской' : 'Женский',
        };
    }
}
