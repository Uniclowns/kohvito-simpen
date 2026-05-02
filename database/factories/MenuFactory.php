<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model Menu.
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_kategori'          => fake()->numberBetween(1, 5),
            'nama_menu'            => fake()->words(3, true),
            'deskripsi'            => fake()->sentence(),
            'harga'                => fake()->randomElement([15000, 18000, 20000, 25000, 28000, 30000, 35000, 40000, 45000]),
            'gambar_menu'          => 'menu/default.png',
            'status_ketersediaan'  => fake()->randomElement(['Tersedia', 'Tidak Tersedia']),
        ];
    }
}
