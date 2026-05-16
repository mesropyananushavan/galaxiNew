<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    public function definition(): array
    {
        $verb = fake()->randomElement(['Review', 'Manage', 'Assign', 'Activate']);
        $resource = fake()->randomElement(['Card Holders', 'Cards', 'Card Types', 'Roles']);
        $name = sprintf('%s %s', $verb, $resource);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'review_note' => 'Phase 1 seeded permission for Galaxy authorization-baseline validation.',
        ];
    }
}
