<?php

namespace Tests\Feature;

use App\Http\Controllers\Contacts\ContactsGeneralController;
use App\Models\PATIENTS;
use GuzzleHttp\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadChildTest extends TestCase
{
    use RefreshDatabase;
    private static $amoFatherID = 20284111;
    private static $amoChildID_f = 20284239; // With FIO only
    private static $amoChildID_b = 20284357; // With BirthDate only
    private static $amoChildID_bf = 20284801; // With FIO and birthday

    /**
     * A basic feature test example.
     */
    public function testWhenChildHasOnlyFio(): void
    {
        $pat_id = PATIENTS::create([
            'NOM' => 'Чацкий',
            'PRENOM'    => 'Петр',
            'PATRONYME' => 'Иванович',
            'MOBIL_NYY' => '1234567891',
        ])->PATIENTS_ID;
        $patData = PATIENTS::find($pat_id)->toArray();
        $patData['FIO'] = 'Чацкий Петр Иванович';
        $client = new Client(['verify'=>false]);
        $contactClass = new ContactsGeneralController($client);
        $contactAmoID = $contactClass->AccrossGetRequests($patData);
        $this->assertEquals(self::$amoChildID_f,$contactAmoID);
    }

    /**
     * A basic feature test example.
     */
    public function testWhenChildHasOnlyBirthday(): void
    {
        $pat_id = PATIENTS::create([
            'NOM' => 'Чацкий',
            'PRENOM'    => 'Петр',
            'PATRONYME' => 'Иванович',
            'MOBIL_NYY' => '1234567891',
        ])->PATIENTS_ID;
        $patData = PATIENTS::find($pat_id)->toArray();
        $patData['agePat'] = '15';
        $client = new Client(['verify'=>false]);
        $contactClass = new ContactsGeneralController($client);
        $contactAmoID = $contactClass->AccrossGetRequests($patData);
        $this->assertEquals(self::$amoChildID_b,$contactAmoID);
    }

    /**
     * A basic feature test example.
     */
    public function testWhenChildHasboth(): void
    {
        $pat_id = PATIENTS::create([
            'NOM' => 'Чацкая',
            'PRENOM'    => 'Вера',
            'PATRONYME' => 'Ивановна',
            'MOBIL_NYY' => '1234567891',
        ])->PATIENTS_ID;
        $patData = PATIENTS::find($pat_id)->toArray();
        $patData['agePat'] = '9';
        $patData['FIO'] = 'Чацкая Вера Ивановна';
        $client = new Client(['verify'=>false]);
        $contactClass = new ContactsGeneralController($client);
        $contactAmoID = $contactClass->AccrossGetRequests($patData);
        $this->assertEquals(self::$amoChildID_bf,$contactAmoID);
    }
}
