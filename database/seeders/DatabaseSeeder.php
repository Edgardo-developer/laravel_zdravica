<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\AmoCrmLead;
use App\Models\AmoCrmTable;
use App\Models\PATIENTS;
use App\Models\PLANNING;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        AmoCRMLead::factory()->create();
        PLANNING::factory()->create();
        PATIENTS::factory()->create();
        AmoCrmTable::factory()->create();
        PLANNING::create([
            'NOM'   => 'Фамилие1',
            'PRENOM'    => 'Имя1',
            'PATRONYME' => 'Отчество1'
        ]);
        PATIENTS::create([
            'NOM' => "Фамилие1",
            'PRENOM' => "Имя1",
            'PATRONYME' => "Отчество1",
            'EMAIL' => "email@email.com",
            'MOBIL_NYY' => "1234567891",
            'POL' => 1,
            'GOROD' => 'Tbilisi',
            'NE_LE' => '2010-01-01',
            'RAYON_VYBORKA' => 'address',
            'ULICA' => 'ulica',
            'DOM' => 'dom',
            'KVARTIRA' => 'kvartira',
        ]);
    }
}
