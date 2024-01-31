<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AmoCrmLead>
 */
class AmoCRMLeadFactory extends Factory
{
    protected $model = 'App\Models\AmoCrmLead';
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'direction' => 'терапия',
            'specDoc'   => 'Врач-терапевт',
            'patID' => 1,
            'leadDBId' => 1,
            'fioDoc'    => fake()->name(1),
            'fioPat'    => 'Имя Фамилие',
            'billID'    => NULL,
            'billSum'   => NULL,
            'offers'    => 'Общий анализ крови',
            'managerName'   => fake()->name,
            'amoManagerID'  => 1,
            'declareCall'  => true,
            'filial'    => 'filial',
            'date'  => fake()->date,
            'created_at'    => time(),
            'updated_at'    => time(),
        ];
    }
}
