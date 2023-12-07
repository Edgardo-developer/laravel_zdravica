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
            'NOM' => fake()->name(),
            'PRENOM' => fake()->name(),
            'PATRONYME' => fake()->name(),
            'EMAIL' => fake()->email(),
            'MOBIL_NYY' => fake()->numberBetween(1111111, 9999999),
            'POL' => fake()->numberBetween(0, 1),
            'GOROD' => fake()->city(),
            'NE_LE' => fake()->date('Y.m.d', 'now - 18 years'),
            'RAYON_VYBORKA' => fake()->address,
            'ULICA' => fake()->address(),
            'DOM' => fake()->address(),
            'KVARTIRA' => fake()->address(),
        ];
    }
}
