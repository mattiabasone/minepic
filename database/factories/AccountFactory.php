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
            'uuid'      => $this->faker->uuid,
            'username'  => $this->faker->name,
            'skin_md5'  => $this->faker->md5,
            'fail_count' => $this->faker->randomNumber(4),
            'updated'   => $this->faker->unixTime,
            'skin'      => $this->faker->sha256,
            'cape'      => $this->faker->sha256,
        ];
    }
}