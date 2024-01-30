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
    }
}
