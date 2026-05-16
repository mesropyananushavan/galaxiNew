<?php

namespace Database\Factories;

use App\Models\CardHolder;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CardHolder>
 */
class CardHolderFactory extends Factory
{
    protected $model = CardHolder::class;

    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'full_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'is_active' => true,
            'review_note' => 'Phase 1 seeded card holder for Galaxy card-domain baseline validation.',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
            'review_note' => 'Inactive seeded card holder for lifecycle coverage.',
        ]);
    }
}
