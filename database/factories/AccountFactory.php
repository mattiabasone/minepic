<?php

namespace Database\Factories;

use Minepic\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Account::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid'      => str_replace('-', '', $this->faker->uuid),
            'username'  => $this->faker->name,
            'fail_count' => $this->faker->randomNumber(1),
            'skin'      => $this->faker->sha256,
            'cape'      => $this->faker->sha256,
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}