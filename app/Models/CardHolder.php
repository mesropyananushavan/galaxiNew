<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['shop_id', 'full_name', 'phone', 'email', 'is_active'])]
class CardHolder extends Model
{
    use HasFactory;

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
