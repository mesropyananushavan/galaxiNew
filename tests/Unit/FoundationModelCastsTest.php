<?php

namespace Tests\Unit;

use App\Models\CardHolder;
use App\Models\Shop;
use Tests\TestCase;

class FoundationModelCastsTest extends TestCase
{
    public function test_shop_casts_is_active_attribute_to_boolean(): void
    {
        $shop = new Shop([
            'name' => 'Galaxy Cast Shop',
            'code' => 'galaxy-cast-shop',
            'is_active' => 1,
        ]);

        $this->assertTrue($shop->is_active);
    }

    public function test_cardholder_casts_is_active_attribute_to_boolean(): void
    {
        $cardHolder = new CardHolder([
            'full_name' => 'Galaxy Cast Holder',
            'is_active' => 1,
        ]);

        $this->assertTrue($cardHolder->is_active);
    }
}
