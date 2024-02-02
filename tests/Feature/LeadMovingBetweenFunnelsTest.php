<?php

namespace Tests\Feature;

use App\Http\Controllers\Leads\LeadGeneralController;
use App\Http\Controllers\Sends\DeleteLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use GuzzleHttp\Client;
use Tests\TestCase;

class LeadMovingBetweenFunnelsTest extends TestCase
{
    /**
     * A basic feature test example.
     */
//    public function testFromUnassembled(): void
//    {
//        $findedArray = AmoCRMLead::find(1);
//        $leadArray = $findedArray->toArray();
//        $this->assertIsArray($leadArray);
//
//        $client = new Client(['verify'=>false]);
//        $LeadGeneralController = new LeadGeneralController($client);
//        $leadPrepared = $LeadGeneralController->prepare($leadArray, 20264511);
//        $leadID = $LeadGeneralController->create($leadPrepared,7332486);
//        $this->assertGreaterThan(100,$leadID);
//        $leadArray['amoContactID'] = 20264511;
//        $leadArray['amoLeadID'] = $leadID;
//        $leadArray['status_id'] = 61034286;
//        $response = $LeadGeneralController->update($leadArray);
//        $this->assertEquals(200,$response->getStatusCode());
//    }

    public function testFromFirst(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $leadDB = $findedArray->toArray();
        $this->assertIsArray($leadDB);

        $client = new Client(['verify'=>false]);
        $LeadGeneralController = new LeadGeneralController($client);
        $preparedLead = $LeadGeneralController->prepare($leadDB, 20284111);
        $leadID = $LeadGeneralController->create($preparedLead,61034282);
        $this->assertGreaterThan(100,$leadID);

        $preparedLead['amoContactID'] = 20264511;
        $preparedLead['amoLeadID'] = $leadID;
        $response = $LeadGeneralController->update($preparedLead);
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testToSuccess(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$SendToAmoCRMArr['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(false));
    }

    public function testToFailure(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$SendToAmoCRMArr['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(true));
    }
}
