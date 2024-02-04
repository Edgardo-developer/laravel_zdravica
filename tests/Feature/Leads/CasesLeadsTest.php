<?php


namespace Leads;

use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Sends\UpdateLeadController;
use App\Http\Controllers\SendToAmoCRM;
use App\Models\AmoCrmLead;
use GuzzleHttp\Client;
use Tests\TestCase;

class CasesLeadsTest extends TestCase
{
    private static $amoCurrentEmptyLeadID = 13632087;

    public function testWhenLeadIsEmpty()
    {
        $client = new Client(['verify' => false]);
        $leadController = new LeadController($client);
        $amoID = $leadController->getAmoID(['amoContactID' => 20284111]);
        $this->assertEquals(self::$amoCurrentEmptyLeadID, $amoID);
    }
}
