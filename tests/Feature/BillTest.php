<?php

namespace Tests\Feature;

use App\Http\Controllers\Bill\BillController;
use App\Http\Controllers\LeadLinks\LeadLinksController;
use GuzzleHttp\Client;
use Tests\TestCase;

class BillTest extends TestCase
{
    /**
     * A basic feature test example.
     */
//    public function testLinkBillToLead(): void
//    {
//        $client = new Client(['verify'=>false]);
//        $Bill = new BillController($client);
//        $offersData = [
//            'offerNames'    => [
//                'Новая услуга',
//                'Эстеразный ингибитор С1 комплемента - функциональный',
//            ],
//            'offerPrices'    => [
//                5000,
//                6000
//            ],
//        ];
//        $billDB = [
//            'offers' => $offersData,
//            'price' => 11000,
//            'status' => 'Создан',
//        ];
//        $billID = $Bill->createBill($billDB,0);
//        $this->assertGreaterThan(0,$billID);
//
//        $leadDBJustContact = ['amoContactID' => 20264511];
//        $LeadLinksController = new LeadLinksController($client);
//        $preparedData = $LeadLinksController->prepare($leadDBJustContact,$billID);
//        $response = $LeadLinksController->create($preparedData, 13983829);
//        $this->assertEquals(200,$response->getStatusCode());
//    }

    public function testBillUpdate(): void
    {
        $client = new Client(['verify'=>false]);
        $Bill = new BillController($client);
        $offersData1 = [
            'offerNames'    => [
                'Новая услуга',
                'Эстеразный ингибитор С1 комплемента - функциональный',
            ],
            'offerPrices'    => [
                5000,
                6000
            ],
        ];
        $offersData2 = [
            'offerNames'    => [
                'Эстеразный ингибитор С1 комплемента - функциональный',
            ],
            'offerPrices'    => [
                6000
            ],
        ];
        $billDB = [
            'offers' => $offersData1,
            'price' => 6000,
            'status' => 'Создан',
            'id'    => 466057,
        ];
        $billResponse = $Bill->updateBill($billDB,0);
        $this->assertEquals(200,$billResponse->getStatusCode());

        $billDB['offers'] = $offersData2;
        $billResponse = $Bill->updateBill($billDB,0);
        $this->assertEquals(200,$billResponse->getStatusCode());
    }
}
