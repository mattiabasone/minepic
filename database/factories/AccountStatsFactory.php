<?php

namespace Database\Factories;

use Minepic\Models\AccountStats;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountStatsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountStats::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid' => $this->faker->uuid,
            'count_request' => $this->faker->randomNumber(6),
            'request_at' => $this->faker->dateTime,
        ];
    }
}