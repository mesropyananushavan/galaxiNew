<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    protected $model = Shop::class;

    public function definition(): array
    {
        $city = fake()->unique()->city();
        $code = Str::of($city)->slug()->substr(0, 12)->upper();

        return [
            'name' => sprintf('Galaxy %s Shop', $city),
            'code' => (string) $code,
            'is_active' => true,
            'review_note' => 'Phase 1 seeded shop for scoped admin and card-domain validation.',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
            'review_note' => 'Phase 1 seeded inactive shop for access-baseline coverage.',
        ]);
    }
}
