<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopeReviewNoted(Builder $query): Builder
    {
        return $query->whereNotNull('review_note')->where('review_note', '!=', '');
    }

    public function scopeActivationNoted(Builder $query): Builder
    {
        return $query->whereNotNull('activation_note')->where('activation_note', '!=', '');
    }

    public function scopeRolloutNoted(Builder $query): Builder
    {
        return $query->whereNotNull('rollout_note')->where('rollout_note', '!=', '');
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
