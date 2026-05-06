<?php

namespace Tests\Unit;

use App\Models\Card;
use App\Models\CardHolder;
use App\Models\Shop;
use Illuminate\Support\Carbon;
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

    public function test_card_casts_issued_at_attribute_to_datetime(): void
    {
        $card = new Card([
            'number' => 'GX-CAST-1001',
            'status' => 'draft',
            'issued_at' => '2026-05-06 04:35:00',
        ]);

        $this->assertInstanceOf(Carbon::class, $card->issued_at);
        $this->assertSame('2026-05-06 04:35:00', $card->issued_at?->format('Y-m-d H:i:s'));
    }
}
