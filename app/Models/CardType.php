<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'points_rate', 'is_active'])]
class CardType extends Model
{
    use HasFactory;

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
