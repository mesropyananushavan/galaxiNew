<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'points_rate', 'is_active', 'review_note', 'activation_note', 'rollout_note'])]
class CardType extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'points_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
