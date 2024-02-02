<?php

namespace Leads;

use App\Http\Controllers\Sends\UpdateLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

//    public function testLeadCreated()
//    {
//        $array = AmoCRMLead::find(1);
//        $array = $array->toArray();
//        $SendToAmoCRM = new SendToAmoCRM($array);
//        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();
//        $this->assertIsArray($SendToAmoCRMArr);
//        $this->assertNotEmpty($SendToAmoCRMArr);
//        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoContactID']);
//        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoLeadID']);
//        $this->assertGreaterThan(0, $SendToAmoCRMArr['leadDBId']);
//    }

    public function testLeadUpdated()
    {
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $this->assertIsArray($SendToAmoCRMArr);
        $this->assertNotEmpty($SendToAmoCRMArr);
        $SendToAmoCRMArr['offerLists'] = 'Эпидурография:2000,Эстеразный ингибитор С1 комплемента - функциональный:3000';
        $SendToAmoCRMArr['billSum'] = 5000;

        $SendToAmoCRMUpdate = new UpdateLeadController($SendToAmoCRMArr);
        $updated = $SendToAmoCRMUpdate->sendDealToAmoCRM();

        $this->assertIsArray($updated);
        $this->assertNotEmpty($updated);
        $this->assertGreaterThan(0, $updated['amoBillID']);
        $this->assertGreaterThan(0, $updated['billSum']);
    }
}
