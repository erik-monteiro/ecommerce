<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->text(20);
        $price = $this->faker->numberBetween($min = 50, $max = 300);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->text(70),
            'image_name' => $this->faker->imageUrl($width = 140, $height = 300),
            'price' => $price,
            'sale_price' => $price + 50,
        ];
    }
}
