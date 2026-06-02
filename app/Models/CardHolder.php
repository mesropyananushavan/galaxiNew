<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['shop_id', 'full_name', 'phone', 'email', 'is_active', 'review_note'])]
class CardHolder extends Model
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

    public function scopeAssignedToActiveShop(Builder $query): Builder
    {
        return $query->whereHas('shop', fn (Builder $shopQuery): Builder => $shopQuery->active());
    }

    public function scopeAssignedToPausedShop(Builder $query): Builder
    {
        return $query->whereHas('shop', fn (Builder $shopQuery): Builder => $shopQuery->where('is_active', false));
    }

    public function scopeLinked(Builder $query): Builder
    {
        return $query->has('cards');
    }

    public function scopeActiveLinked(Builder $query): Builder
    {
        return $query->active()->linked();
    }

    public function scopeInactiveLinked(Builder $query): Builder
    {
        return $query->where('is_active', false)->linked();
    }

    public function scopeActiveUnlinked(Builder $query): Builder
    {
        return $query->active()->doesntHave('cards');
    }

    public function scopeInactiveUnlinked(Builder $query): Builder
    {
        return $query->where('is_active', false)->doesntHave('cards');
    }

    public function scopeBlockedLinked(Builder $query): Builder
    {
        return $query->whereHas('cards', fn (Builder $cardQuery): Builder => $cardQuery->where('status', 'blocked'));
    }

    public function scopeDraftLinked(Builder $query): Builder
    {
        return $query->whereHas('cards', fn (Builder $cardQuery): Builder => $cardQuery->where('status', 'draft'));
    }

    public function scopeCardActiveLinked(Builder $query): Builder
    {
        return $query->whereHas('cards', fn (Builder $cardQuery): Builder => $cardQuery->where('status', 'active'));
    }

    public function scopeActivatedLinked(Builder $query): Builder
    {
        return $query->whereHas('cards', fn (Builder $cardQuery): Builder => $cardQuery->whereNotNull('activated_at'));
    }

    public function scopeAssignedToActiveShopLinked(Builder $query): Builder
    {
        return $query->assignedToActiveShop()->linked();
    }

    public function scopeAssignedToPausedShopUnlinked(Builder $query): Builder
    {
        return $query->assignedToPausedShop()->doesntHave('cards');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(Card::class);
    }
}
