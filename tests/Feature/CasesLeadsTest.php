<?php


use App\Http\Controllers\Sends\UpdateLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CasesLeadsTest extends TestCase
{
    use RefreshDatabase;
    public function testWhenPatientIsAChild(): void
    {
        $array = AmoCRMLead::find(1);
        $array = $array->toArray();
        $array['patID'] = 1;
        $array['leadDBId'] = 2;
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();
        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoContactID']);
        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoLeadID']);
    }

    public function testWhenPatientIsEmptyButHasNumber(): void
    {
        $array = AmoCRMLead::find(1);
        $array = $array->toArray();
        $array['patID'] = NULL;
        $array['leadDBId'] = 1;
        $SendToAmoCRM = new SendToAmoCRM($array);
        $SendToAmoCRMArr = $SendToAmoCRM->sendDealToAmoCRM();
        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoContactID']);
        $this->assertGreaterThan(0, $SendToAmoCRMArr['amoLeadID']);

        $SendToAmoCRMArr['patID'] = 2;
        $SendToAmoCRMArr['patID_changed'] = true;
        $update = new UpdateLeadController($SendToAmoCRMArr);
        $doneUpdate = $update->sendDealToAmoCRM();
        $this->assertIsArray($doneUpdate);
        $this->assertNotEmpty($doneUpdate);
        $this->assertGreaterThan(0, $doneUpdate['amoContactID']);
        $this->assertGreaterThan(0, $doneUpdate['amoLeadID']);

        $this->assertEquals($SendToAmoCRMArr['amoContactID'], $doneUpdate['amoContactID']);
        $this->assertEquals($SendToAmoCRMArr['amoLeadID'], $doneUpdate['amoLeadID']);
    }
}
