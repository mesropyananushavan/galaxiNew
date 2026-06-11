<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        $name = fake()->unique()->jobTitle();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
            'review_note' => 'Phase 1 seeded role for Galaxy access-baseline validation.',
            'access_note' => 'Seeded for shop-scoped admin validation.',
            'assignment_note' => 'Attach to seeded operators when bootstrapping local parity fixtures.',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
            'review_note' => 'Inactive seeded role for readiness and gating coverage.',
        ]);
    }
}
