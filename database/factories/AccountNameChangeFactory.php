<?php

namespace Database\Factories;

use Minepic\Models\AccountNameChange;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountNameChangeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AccountNameChange::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'uuid' => str_replace('-', '', $this->faker->uuid),
            'prev_name' => $this->faker->name,
            'new_name' => $this->faker->name,
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}