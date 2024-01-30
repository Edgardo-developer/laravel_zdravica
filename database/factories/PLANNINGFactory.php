<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PLANNING>
 */
class PLANNINGFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'NOM'   => 'Test',
            'PRENOM'    => 'Test',
            'PATRONYME' => 'Test'
        ];
    }
}
