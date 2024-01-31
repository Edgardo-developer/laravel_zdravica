<?php

namespace Tests\Feature;

use App\Http\Controllers\Sends\DeleteLeadController;
use App\Http\Controllers\Sends\UpdateLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadDeleteTest extends TestCase
{
    use RefreshDatabase;
    public function testLeadDeleteWithSuccess(){
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $this->assertIsArray($SendToAmoCRMArr);
        $this->assertNotEmpty($SendToAmoCRMArr);
        $SendToAmoCRMArr['offerLists'] = 'Эпидурография:2000,Эстеразный ингибитор С1 комплемента - функциональный:3000';

        $SendToAmoCRMUpdate = new UpdateLeadController($SendToAmoCRMArr);
        $updated = $SendToAmoCRMUpdate->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$updated['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(false));
    }

    public function testLeadDeleteWithReason(){
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $this->assertIsArray($SendToAmoCRMArr);
        $this->assertNotEmpty($SendToAmoCRMArr);
        $SendToAmoCRMArr['offerLists'] = 'Эпидурография:2000,Эстеразный ингибитор С1 комплемента - функциональный:3000';

        $SendToAmoCRMUpdate = new UpdateLeadController($SendToAmoCRMArr);
        $updated = $SendToAmoCRMUpdate->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$updated['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(true));
    }
}
