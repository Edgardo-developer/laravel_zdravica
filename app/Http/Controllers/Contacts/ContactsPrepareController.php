<?php

namespace App\Http\Controllers\Contacts;


use App\Http\Controllers\PrepareEntityController;


class ContactsPrepareController extends PrepareEntityController
{
    private static array $amoFields = [
        'name',
        'first_name',
        'last_name',
        'custom_fields_values' => [
            'mobile' => 170783,
            'email' => 170785,
            'FIO' => 391181,
            'Birthday' => 391183,
            'POL' => 391185,
            'GOROD' => 1573507,
            'RAYON_VYBORKA' => 1573509,
            'ULICA' => 1573511,
            'DOM' => 1573513,
            'KVARTIRA' => 1573515,
            'NUMBER' => 1573517,
            'Doverenni' => 1573519,
            'Doljnost' => 170781,
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
    public static function prepare(array $contactDB, $contactID = 0): array
    {
        $prepared = [];
        foreach (self::$amoFields as $mergedContactField) {
            if (is_string($mergedContactField)) {
                if (!$contactID) {
                    $prepared[$mergedContactField] = self::matchFields($mergedContactField, $contactDB);
                }
            } else {
                foreach ($mergedContactField as $customFieldsName => $customFieldsID) {
                    if ((!$contactID && !in_array($customFieldsName, self::$secondRound))
                        ||
                        ($contactID && in_array($customFieldsName, self::$secondRound))) {
                        $val = self::matchFields($customFieldsName, $contactDB);
                        if ($val && $val !== 'null') {
                            if ($contactID === 0 && $mergedContactField === 'mobile') {
                                $val = '8' . $val;
                            }
                            $prepared['custom_fields_values'][] = [
                                'field_id' => $customFieldsID,
                                'values' => [['value' => $val]],
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
        return match ($mergedContactField) {
            'name' => $contactDB['NOM'] . ' ' . $contactDB['PRENOM'],
            'first_name' => $contactDB['NOM'] ?? '',
            'last_name' => $contactDB['PRENOM'] ?? '',
            'created_by' => $contactDB['created_at'] ?? '',
            'updated_by' => $contactDB['updated_at'] ?? '',
            'mobile', => $contactDB['MOBIL_NYY'] ?? '',
            'email', => $contactDB['EMAIL'] ?? '',
            'GOROD', => $contactDB['GOROD'] ?? '',
            'FIO', => $contactDB['PRENOM'] . ' ' . $contactDB['NOM'] . ' ' . $contactDB['PATRONYME'],
            'Birthday', => $contactDB['NE_LE'] ?? '',
            'POL', => self::checkPol($contactDB['NOM'], $contactDB['PATRONYME']),
            'RAYON_VYBORKA' => $contactDB['RAYON_VYBORKA'],
            'ULICA' => $contactDB['ULICA'] ?? '',
            'DOM' => $contactDB['DOM'] ?? '',
            'KVARTIRA' => $contactDB['KVARTIRA'] ?? '',
            'NUMBER' => $contactDB['NUMBER'] ?? '',
            'Doverenni' => $contactDB['Doverenni'] ?? '',
        };
    }

    private static function checkPol($lastName, $fatherName): string
    {
        $lastLetters = ['а', 'я'];
        if (
            !in_array(substr($lastName, -2), $lastLetters)
            &&
            !in_array(substr($fatherName, -2), $lastLetters)
        ) {
            return 'Мужской';
        }
        return 'Женский';
    }
}
