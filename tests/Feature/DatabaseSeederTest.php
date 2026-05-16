<?php

namespace Tests\Feature;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_bootstraps_galaxy_foundation_records(): void
    {
        $this->seed();

        $shop = Shop::query()->where('code', 'HQ')->first();
        $role = Role::query()->where('slug', 'shop-admin')->first();
        $operator = User::query()->where('email', 'shop-admin@galaxy.local')->first();
        $bootstrapAdmin = User::query()->where('email', 'bootstrap-admin@galaxy.local')->first();
        $cardType = CardType::query()->where('slug', 'galaxy-prime')->first();
        $holder = CardHolder::query()->where('email', 'holder@galaxy.local')->first();
        $card = Card::query()->where('number', 'GX-000001')->first();

        $this->assertNotNull($shop);
        $this->assertNotNull($role);
        $this->assertNotNull($operator);
        $this->assertNotNull($bootstrapAdmin);
        $this->assertNotNull($cardType);
        $this->assertNotNull($holder);
        $this->assertNotNull($card);

        $this->assertTrue($operator->roles->contains($role));
        $this->assertTrue($role->permissions->pluck('slug')->contains('review-card-holders'));
        $this->assertSame($shop->getKey(), $operator->shop_id);
        $this->assertSame($shop->getKey(), $holder->shop_id);
        $this->assertSame($shop->getKey(), $card->shop_id);
        $this->assertSame($holder->getKey(), $card->card_holder_id);
        $this->assertSame($cardType->getKey(), $card->card_type_id);
        $this->assertSame(3, Permission::query()->count());
    }
}
