<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'code', 'is_active', 'review_note'])]
class Shop extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeReviewNoted(Builder $query): Builder
    {
        return $query->whereNotNull('review_note')->where('review_note', '!=', '');
    }

    public function scopeManagerAssigned(Builder $query): Builder
    {
        return $query->whereHas('users');
    }

    public function scopeFoundationCovered(Builder $query): Builder
    {
        return $query->where(function (Builder $coverageQuery): void {
            $coverageQuery
                ->has('cardHolders')
                ->orHas('cards');
        });
    }

    public function scopeRoleCovered(Builder $query): Builder
    {
        return $query->whereHas('users.roles');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function cardHolders(): HasMany
    {
        return $this->hasMany(CardHolder::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
