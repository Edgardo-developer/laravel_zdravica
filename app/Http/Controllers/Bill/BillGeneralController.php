<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillGeneralController extends Controller
{
    public function __construct($client){
        $this->client = $client;
    }

    public function builder($billID){
        return [
            'id' => $billID,
            'custom_fields_values' => [
                [
                    'field_id' => 1550048,
                    'values' => [
                        [
                            'value' => 'Оплачен',
                        ]
                    ]
                ]
            ],
        ];
    }

    public function prepare($billDB, $billStatus){
        return (new BillPrepareController($billDB, $billStatus))->prepare();
    }

    public function getAmoID($billDB){
        $prepared = $this->prepare($billDB,$billDB['billStatus']);
        return BillRequestController::create($this->client, $prepared);
    }

    public function updateBill($billDB) : void{
        $prepared = $this->prepare($billDB,$billDB['billStatus']);
        BillRequestController::update($this->client, $prepared);
    }
}
