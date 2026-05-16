<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $shop = Shop::query()->firstOrCreate(
            ['code' => 'HQ'],
            [
                'name' => 'Galaxy HQ Shop',
                'is_active' => true,
                'review_note' => 'Phase 1 baseline shop for local migration and admin-shell validation.',
            ],
        );

        $role = Role::query()->firstOrCreate(
            ['slug' => 'shop-admin'],
            [
                'name' => 'Shop Admin',
                'is_active' => true,
                'review_note' => 'Phase 1 baseline role for local authorization validation.',
                'access_note' => 'Use for seeded shop operators during local parity checks.',
                'assignment_note' => 'Assigned automatically to the default seeded operator.',
            ],
        );

        $permissions = collect([
            ['name' => 'Review Card Holders', 'slug' => 'review-card-holders'],
            ['name' => 'Manage Cards', 'slug' => 'manage-cards'],
            ['name' => 'Manage Card Types', 'slug' => 'manage-card-types'],
        ])->map(fn (array $permission): Permission => Permission::query()->firstOrCreate(
            ['slug' => $permission['slug']],
            [
                'name' => $permission['name'],
                'review_note' => 'Phase 1 baseline permission for Galaxy access validation.',
            ],
        ));

        $role->permissions()->syncWithoutDetaching($permissions->pluck('id')->all());

        $operator = User::query()->firstOrCreate(
            ['email' => 'shop-admin@galaxy.local'],
            [
                'shop_id' => $shop->getKey(),
                'name' => 'Galaxy Shop Admin',
                'password' => Hash::make('password'),
            ],
        );

        $operator->roles()->syncWithoutDetaching([$role->getKey()]);

        User::query()->firstOrCreate(
            ['email' => 'bootstrap-admin@galaxy.local'],
            [
                'name' => 'Galaxy Bootstrap Admin',
                'password' => Hash::make('password'),
            ],
        );

        $cardType = CardType::query()->firstOrCreate(
            ['slug' => 'galaxy-prime'],
            [
                'name' => 'Galaxy Prime',
                'points_rate' => 1.75,
                'is_active' => true,
                'review_note' => 'Phase 1 baseline card type for local card-domain validation.',
                'activation_note' => 'Seeded to support early activation workflow previews.',
                'rollout_note' => 'Use until real parity data lands from galaxiOld.',
            ],
        );

        $holder = CardHolder::query()->firstOrCreate(
            ['email' => 'holder@galaxy.local'],
            [
                'shop_id' => $shop->getKey(),
                'full_name' => 'Galaxy Test Holder',
                'phone' => '+10000000001',
                'is_active' => true,
                'review_note' => 'Phase 1 baseline card holder for local admin-shell validation.',
            ],
        );

        Card::query()->firstOrCreate(
            ['number' => 'GX-000001'],
            [
                'shop_id' => $shop->getKey(),
                'card_holder_id' => $holder->getKey(),
                'card_type_id' => $cardType->getKey(),
                'status' => 'active',
                'issued_at' => now()->subDays(30),
                'activated_at' => now()->subDays(29),
                'review_note' => 'Phase 1 baseline seeded card for local migration validation.',
            ],
        );
    }
}
