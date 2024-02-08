<?php

namespace Leads;

use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Sends\DeleteLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadMovingBetweenFunnelsTest extends TestCase
{
    use RefreshDatabase;
    public function testFromFirst(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $leadDB = $findedArray->toArray();
        $this->assertIsArray($leadDB);

        $client = new Client(['verify'=>false]);
        $LeadController = new LeadController($client);
        $preparedLead = $LeadController->prepare($leadDB, 20284111);
        $leadID = $LeadController->create($preparedLead,61034282);
        $this->assertGreaterThan(100,$leadID);

        $preparedLead['amoContactID'] = 20264511;
        $preparedLead['amoLeadID'] = $leadID;
        $preparedLead['status_id'] = 61034286;
        $response = $LeadController->update($preparedLead);
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
        self::assertEquals(200,$delete->deleteLeads(false)->getStatusCode());
    }

    public function testToFailure(): void
    {
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$SendToAmoCRMArr['amoLeadID']]);
        self::assertEquals(200,$delete->deleteLeads(true)->getStatusCode());
    }
}
