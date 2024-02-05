<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;

class LeadPrepareController extends Controller
{
    private static array $amoFields = [
        'name',
        'price',
        'responsible_user_id',

        'custom_fields_values' => [
            'direction' => 454373,
            'filial' => 454375,
            'fioDoc' => 454379,
            'fioPat' => 1592997,
            'offers' => 454381,
            'agePat' => 1586091,
            'specDoc' => 454377,
            'date' => 1581797,
            'responsibleFIO' => 1572983,
        ],
    ];

    /**
     * @param array $leadDB
     * @param int $contactId
     * @return array
     * Description: prepares the array of the lead
     */
    public function prepare(array $leadDB, int $contactId): array
    {
        $prepared =
            $contactId > 0 ?
            ['_embedded' => ['contacts' => [['id' => $contactId]]]] : [];
        foreach (self::$amoFields as $fieldValue) {
            if ($fieldValue === 'responsible_user_id') {
                continue;
            }
            if (is_string($fieldValue)) {
                $prepared[$fieldValue] = self::matchFields($fieldValue, $leadDB);
            } else {
                foreach ($fieldValue as $subFieldName => $subFieldID) {
                    $val = self::matchFields($subFieldName, $leadDB);
                    if ($val && $val !== 'null') {
                        $prepared['custom_fields_values'][] = [
                            'field_id' => $subFieldID,
                            'values' => [['value' => self::matchFields($subFieldName, $leadDB)]]
                        ];
                    }
                }
            }
        }
        return $prepared;
    }

    /**
     * @param string $mergedLeadFields
     * @param array $leadDB
     * @return mixed|string
     * Description: sets the new values
     */
    private static function matchFields(string $mergedLeadFields, array $leadDB): mixed
    {
        return match ($mergedLeadFields) {
            'name' => $leadDB['leadDBId'],
            'price' => (int)$leadDB['billSum'],
            'direction' => $leadDB['direction'],
            'filial' => $leadDB['filial'],
            'fioDoc' => $leadDB['fioDoc'],
            'fioPat' => self::getFIO($leadDB),
            'agePat' => isset($leadDB['agePat']) ? (string)$leadDB['agePat'] : 'null',
            'offers' => $leadDB['offers'],
            'specDoc' => $leadDB['specDoc'],
            'responsible_user_id' => $leadDB['responsible_user_id'] === 'NULL' ? 10182090 : $leadDB['responsible_user_id'],
            'date' => strtotime($leadDB['date']),
            'responsibleFIO' => $leadDB['responsibleFIO'],
            'declareVisit' => (int)$leadDB['declareVisit'] === 1,
        };
    }

    private static function getFIO($leadDB){
        if (isset($leadDB['fioPat']) && $leadDB['fioPat'] !== ''){
            return $leadDB['fioPat'];
        }
        if (isset($leadDB['FIO']) && $leadDB['FIO'] !== ''){
            return $leadDB['FIO'];
        }
        return '';
    }
}
