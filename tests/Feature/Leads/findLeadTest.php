<?php

namespace Leads;

use App\Http\Controllers\Contacts\ContactsController;
use App\Http\Controllers\Leads\LeadPresendController;
use App\Http\Controllers\Leads\LeadRequestController;
use App\Models\AmoCrmLead;
use App\Models\PATIENTS;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class findLeadTest extends TestCase
{
    use RefreshDatabase;
    private static int $amoLeadFromNerazobrannoe = 14442027;
    private static int $amoContactFromNerazobrannoe = 20224267;
    private static int $amoLeadFromPervichni = 14317291;
    private static int $amoContactFromPervichni = 20284111;

    /**
     * A basic feature test example.
     */
    public function testfromNerazobrannoe(): void
    {
        $client = new Client(['verify'=>false]);
        $LeadPresendController = new LeadPresendController();
        $pat_id = PATIENTS::create([
            'NOM' => 'Прокопьева',
            'PRENOM'    => 'Алина',
            'PATRONYME' => 'Дмитриевна',
            'MOBIL_NYY' => '913 489-09-00',
        ])->PATIENTS_ID;
        $patData = PATIENTS::find($pat_id)->toArray();
        $patData['FIO'] = 'Прокопьева Алиса Дмитриевна';
        $contactClass = new ContactsController($client);
        $contactAmoID = $contactClass->AccrossGetRequests($patData);
        $this->assertEquals(self::$amoContactFromNerazobrannoe,$contactAmoID);
        $id = $LeadPresendController->getAmoID($client,['amoContactID'=>$contactAmoID]);
        $this->assertEquals(self::$amoLeadFromNerazobrannoe,$id);
    }

    /**
     * A basic feature test example.
     */
//    public function testfromPervichni(): void
//    {
//        $client = new Client(['verify'=>false]);
//        $LeadPresendController = new LeadPresendController();
//        $pat_id = PATIENTS::create([
//            'NOM' => 'Чацкий',
//            'PRENOM'    => 'Петр',
//            'PATRONYME' => 'Иванович',
//            'MOBIL_NYY' => '1234567891',
//        ])->PATIENTS_ID;
//
//        $patData = PATIENTS::find($pat_id)->toArray();
//        $patData['FIO'] = 'Чацкий Петр Иванович';
//        $contactClass = new ContactsController($client);
//        $contactAmoID = $contactClass->AccrossGetRequests($patData);
//        $this->assertEquals(self::$amoContactFromPervichni,$contactAmoID);
//        $id = $LeadPresendController->getAmoID($client,['amoContactID'=>$contactAmoID]);
//        $this->assertEquals(self::$amoLeadFromPervichni,$id);
//    }
}
