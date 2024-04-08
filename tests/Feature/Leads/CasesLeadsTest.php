<?php


namespace Leads;

use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Sends\UpdateLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CasesLeadsTest extends TestCase
{
    use RefreshDatabase;
    private static $amoCurrentEmptyLeadID = 16961985;

    public function testWhenLeadIsEmpty()
    {
        $client = new Client(['verify' => false]);
        $leadController = new LeadController($client);
        $amoID = $leadController->getAmoID(['amoContactID' => 19195845]);
        $this->assertEquals(self::$amoCurrentEmptyLeadID, $amoID);
    }
}
