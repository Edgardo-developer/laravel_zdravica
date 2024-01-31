<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patients>
 */
class PatientsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NOM' => "Фамилие",
            'PRENOM' => "Имя",
            'PATRONYME' => "Отчество",
            'EMAIL' => "email@email.com",
            'MOBIL_NYY' => "1234567891",
            'POL' => 1,
            'GOROD' => 'Tbilisi',
            'NE_LE' => '2000.01.01',
            'RAYON_VYBORKA' => 'address',
            'ULICA' => 'ulica',
            'DOM' => 'dom',
            'KVARTIRA' => 'kvartira',
        ];
    }
}
