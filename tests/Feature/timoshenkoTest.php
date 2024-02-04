<?php

namespace Tests\Feature;

use App\Models\PATIENTS;
use App\Models\PLANNING;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class timoshenkoTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $planningID = PLANNING::create([
            'NOM'   => 'СКАЧКОВА',
            'PRENOM'    => 'Екатерина',
            'PATRONYME' => 'Дмитриевна',
            'MOBIL_NYY' => '8953-787-9270',
        ])->PLANNING_ID;
        $patientID = PATIENTS::create([
            'NOM'   => 'СКАЧКОВА',
            'PRENOM'    => 'Екатерина',
            'PATRONYME' => 'Дмитриевна',
        ])->PATIENTS_ID;
    }
}
