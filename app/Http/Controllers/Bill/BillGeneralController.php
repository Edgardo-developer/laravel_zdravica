<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;

class BillGeneralController extends Controller
{
    public function __construct($client){
        $this->BillPrepareController = new BillPrepareController();
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

    public function prepare(array $billDB, int $billStatus){
        return $this->BillPrepareController->prepare($billDB, $billStatus);
    }

    public function getAmoID(array $billDB){
        $prepared = $this->prepare($billDB,$billDB['billStatus']);
        return BillRequestController::create($this->client, $prepared);
    }

    public function updateBill(array $billDB) : void{
        $prepared = $this->prepare($billDB,$billDB['billStatus']);
        BillRequestController::update($this->client, $prepared);
    }
}
