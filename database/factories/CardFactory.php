<?php

namespace Database\Factories;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Card>
 */
class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'card_holder_id' => fn (array $attributes): int => CardHolder::factory()->create([
                'shop_id' => $attributes['shop_id'],
            ])->getKey(),
            'card_type_id' => CardType::factory(),
            'number' => sprintf('GX-%06d', fake()->unique()->numberBetween(1, 999999)),
            'status' => fake()->randomElement(['draft', 'active', 'blocked']),
            'issued_at' => Carbon::instance(fake()->dateTimeBetween('-90 days', 'now')),
            'activated_at' => fake()->boolean(70) ? Carbon::instance(fake()->dateTimeBetween('-60 days', 'now')) : null,
            'review_note' => 'Phase 1 seeded card for Galaxy status-flow and admin-shell validation.',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => 'draft',
            'activated_at' => null,
        ]);
    }
}
