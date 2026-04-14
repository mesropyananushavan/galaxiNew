<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shop_id', 'card_holder_id', 'card_type_id', 'number', 'status', 'activated_at'])]
class Card extends Model
{
    use HasFactory;

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function holder(): BelongsTo
    {
        return $this->belongsTo(CardHolder::class, 'card_holder_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CardType::class, 'card_type_id');
    }

    protected function casts(): array
    {
        return [
            'activated_at' => 'datetime',
        ];
    }
}
