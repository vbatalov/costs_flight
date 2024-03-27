<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Costs>
 */
class CostsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "CS_CODE" => fake()->countryCode,
            "PKKEY" => rand(1,5000),
            "CS_DATEEND" => fake()->date,
            "CS_DATE" => fake()->date,
        ];
    }
}
