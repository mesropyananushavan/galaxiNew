<?php

namespace Database\Factories;

use App\Models\CardType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CardType>
 */
class CardTypeFactory extends Factory
{
    protected $model = CardType::class;

    public function definition(): array
    {
        $name = sprintf('Galaxy %s', fake()->unique()->randomElement(['Silver', 'Gold', 'Prime', 'Family', 'Partner']));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'points_rate' => fake()->randomFloat(2, 0.5, 5),
            'is_active' => true,
            'review_note' => 'Phase 1 seeded card type for Galaxy card-domain baseline validation.',
            'activation_note' => 'Seeded for status-flow previews and admin workspace validation.',
            'rollout_note' => 'Use as a local fixture until real migration data arrives from galaxiOld.',
        ];
    }
}
