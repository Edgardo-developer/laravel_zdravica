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
    public function testFromUnassembled(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $leadPrepared = $findedArray->toArray();
        $this->assertIsArray($leadPrepared);

        $client = new Client(['verify'=>false]);
        $LeadGeneralController = new LeadGeneralController($client);
        $leadID = $LeadGeneralController->create($LeadGeneralController->prepare($leadPrepared, 20264511),7332486);
        $this->assertGreaterThan(100,$leadID);
        $leadPrepared['amoContactID'] = 20264511;
        $leadPrepared['amoLeadID'] = $leadID;
        $leadPrepared['status_id'] = 61034286;
        $response = $LeadGeneralController->update($leadPrepared);
        $this->assertEquals(200,$response->getStatusCode());
    }

    public function testFromFirst(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $leadPrepared = $findedArray->toArray();
        $this->assertIsArray($leadPrepared);

        $client = new Client(['verify'=>false]);
        $LeadGeneralController = new LeadGeneralController($client);
        $leadID = $LeadGeneralController->create($LeadGeneralController->prepare($leadPrepared, 20264511),7332486);
        $this->assertGreaterThan(100,$leadID);
        $leadPrepared['amoContactID'] = 20264511;
        $leadPrepared['amoLeadID'] = $leadID;
        $response = $LeadGeneralController->update($leadPrepared);
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
