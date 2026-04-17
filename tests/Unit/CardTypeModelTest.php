<?php

namespace Tests\Unit;

use App\Models\CardType;
use Tests\TestCase;

class CardTypeModelTest extends TestCase
{
    public function test_card_type_casts_points_rate_and_is_active_attributes(): void
    {
        $cardType = new CardType([
            'name' => 'Galaxy Prime',
            'slug' => 'galaxy-prime',
            'points_rate' => '1.75',
            'is_active' => 1,
        ]);

        $this->assertSame('1.75', $cardType->points_rate);
        $this->assertTrue($cardType->is_active);
    }
}
