<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillPrepareController extends Controller
{
    private static array $amoFields = [
        'custom_fields_values' => [
            'status' => 1550048,
            'account' => 1550052,
            'offers' => 1550058,
            'price' => 1550062,
        ],
    ];

    public function prepare(array $billDB, int $billStatus): array
    {
        $arr = ['custom_fields_values' => []];
        if (isset($billDB['id'])){
            $arr['id'] = (int)$billDB['id'];
            unset($billDB['id']);
        }
        $arr = $this->accross_fields($arr,$billDB);
        if ($billStatus === 1) {
            $arr['custom_fields_values'][] = [
                'field_id' => 1550056,
                'values' => [
                    'value' => time(),
                ]
            ];
        }
        return $arr;
    }

    /**
     * @param array $arr
     * @param array $billDB
     * @return array
     * Description: Walk across all fields
     */
    private function accross_fields(array $arr, array $billDB){
        foreach (self::$amoFields['custom_fields_values'] as $amoFieldName => $amoFieldID) {
            if (isset($billDB[$amoFieldName])) {
                if ($amoFieldName === 'account' && $billDB[$amoFieldName]['entity_id'] === 0){
                    continue;
                }
                $value = $amoFieldName !== 'offers' ? $billDB[$amoFieldName] : self::modifyOffers($billDB[$amoFieldName]);
                if (!$value){
                    continue;
                }
                $locArr = [
                    'field_id' => $amoFieldID,
                    'values' => $amoFieldName !== 'offers' ?
                        [['value' => $value]] : $value
                ];

                $arr['custom_fields_values'][] = $locArr;
            }
        }
        return $arr;
    }

    /**
     * @param array $offers
     * @return array
     * Description: offers adds to the bill
     */
    private static function modifyOffers(array $offers): array
    {
        $offersArr = [];
        foreach ($offers['offerNames'] as $k => $offerName) {
            if (isset($offerName,$offers['offerPrices'][$k])){
                $offersArr[] = [
                    'value' => [
                        'description' => $offerName,
                        'unit_price' => $offers['offerPrices'][$k],
                        'quantity' => 1,
                    ]
                ];
            }
        }
        return $offersArr;
    }
}
