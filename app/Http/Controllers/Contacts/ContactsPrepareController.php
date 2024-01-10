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
            1573507 => 'GOROD',
            1573509 => 'RAYON_VYBORKA',
            1573511 => 'ULICA',
            1573513 => 'DOM',
            1573515 => 'KVARTIRA',
            1573517 => 'NUMBER',
            1573519 => 'Doverenni',
            170781 => 'Doljnost',
        ]
    ];

    private static array $secondRound = [
        'RAYON_VYBORKA',
        'ULICA',
        'DOM',
        'KVARTIRA',
        'NUMBER',
        'Doverenni',
        'Doljnost'
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
                if (!$contactID){
                    $prepared[$mergedContactField] = self::matchFields($mergedContactField, $contactDB);
                }
            }else{
                foreach ($mergedContactField as $customFieldsKey => $customFieldsValue){
                    if ((!$contactID && !in_array($customFieldsValue, self::$secondRound))
                        ||
                        ($contactID && in_array($customFieldsValue, self::$secondRound))){
                        $val = self::matchFields($customFieldsValue, $contactDB);
                        if ($val && $val !== 'null'){
                            if ($contactID === 0 && $mergedContactField === 'mobile'){
                                $val = '8'.$val;
                            }
                            $prepared['custom_fields_values'][] = [
                                'field_id'  =>  $customFieldsKey,
                                'values'    =>  [['value'=> $val]],
                            ];
                        }
                    }
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
            'GOROD',    =>  $contactDB['GOROD'] ?? '',
            'FIO',  =>  $contactDB['PRENOM'] . ' ' . $contactDB['NOM'] . ' ' . $contactDB['PATRONYME'],
            'Birthday', =>  $contactDB['NE_LE'] ?? '',
            'POL',  =>  self::checkPol($contactDB['NOM'], $contactDB['PATRONYME']),
            'RAYON_VYBORKA' => $contactDB['RAYON_VYBORKA'],
            'ULICA' => $contactDB['ULICA'] ?? '',
            'DOM' => $contactDB['DOM'] ?? '',
            'KVARTIRA' => $contactDB['KVARTIRA'] ?? '',
            'NUMBER' => $contactDB['NUMBER'] ?? '',
            'Doverenni' => $contactDB['Doverenni'] ?? '',
        };
    }

    private static function checkPol($lastName, $fatherName) : string{
        $lastLetters = ['а','я'];
        if(
            !in_array(substr($lastName, -2), $lastLetters)
            &&
            !in_array(substr($fatherName, -2), $lastLetters)
        ){
            return 'Мужской';
        }
        return 'Женский';
    }
}
