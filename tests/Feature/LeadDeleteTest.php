<?php

namespace Tests\Feature;

use App\Http\Controllers\Sends\DeleteLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadDeleteTest extends TestCase
{
    use RefreshDatabase;
    public function testLeadDeletedWithSuccess(){
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$SendToAmoCRMArr['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(false));
    }

    public function testLeadDeletedWithReason(){
        $findedArray = AmoCRMLead::find(1);
        $array = $findedArray->toArray();
        $this->assertIsArray($array);
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();

        $delete = new DeleteLeadController([$SendToAmoCRMArr['amoLeadID']]);
        self::assertTrue($delete->deleteLeads(true));
    }
}
