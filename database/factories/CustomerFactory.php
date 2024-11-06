<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition()
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'annualSpend' => $this->faker->randomFloat(2, 100, 10000),
            'lastPurchaseDate' => $this->faker->dateTimeThisYear()->format('c'),
        ];
    }
}
