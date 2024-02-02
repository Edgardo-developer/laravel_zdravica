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
    public function testLinkBillToLead(): void
    {
        $client = new Client(['verify'=>false]);
        $Bill = new BillController($client);
        $offersData = [
            'offerNames'    => [
                'Новая услуга'
            ],
            'offerPrices'    => [
                5000
            ],
        ];
        $billDB = [
            'offers' => $offersData,
            'price' => 5000,
            'billStatus' => 0,
            'status' => 'Создан',
        ];
        $billID = $Bill->createBill($billDB);
        $this->assertGreaterThan(0,$billID);

        $leadDBJustContact = ['amoContactID' => 20284111];
        $LeadLinksController = new LeadLinksController($client);
        $preparedData = $LeadLinksController->prepare($leadDBJustContact,$billID);
        $preparedData['amoLeadID'] = 13620685;
        $response = $LeadLinksController->create($preparedData);
        $this->assertEquals(200,$response->getStatusCode());
    }
}
