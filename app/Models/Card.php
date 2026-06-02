<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['shop_id', 'card_holder_id', 'card_type_id', 'number', 'status', 'issued_at', 'review_note', 'activated_at'])]
class Card extends Model
{
    use HasFactory;

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeBlocked(Builder $query): Builder
    {
        return $query->where('status', 'blocked');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    public function scopeReviewNoted(Builder $query): Builder
    {
        return $query->whereNotNull('review_note')->where('review_note', '!=', '');
    }

    public function scopeIssued(Builder $query): Builder
    {
        return $query->whereNotNull('issued_at');
    }

    public function scopePreActivation(Builder $query): Builder
    {
        return $query->issued()->whereNull('activated_at');
    }

    public function scopeHolderLinked(Builder $query): Builder
    {
        return $query->whereNotNull('card_holder_id');
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('card_holder_id');
    }

    public function scopeIssuedHolderLinked(Builder $query): Builder
    {
        return $query->issued()->holderLinked();
    }

    public function scopeActivatedHolderLinked(Builder $query): Builder
    {
        return $query->whereNotNull('activated_at')->holderLinked();
    }

    public function scopeIssuedUnassigned(Builder $query): Builder
    {
        return $query->issued()->unassigned();
    }

    public function scopePreActivationHolderLinked(Builder $query): Builder
    {
        return $query->preActivation()->holderLinked();
    }

    public function scopePreActivationUnassigned(Builder $query): Builder
    {
        return $query->preActivation()->unassigned();
    }

    public function scopeActiveHolderLinked(Builder $query): Builder
    {
        return $query->active()->holderLinked();
    }

    public function scopeActiveUnassigned(Builder $query): Builder
    {
        return $query->active()->unassigned();
    }

    public function scopeBlockedPreActivation(Builder $query): Builder
    {
        return $query->blocked()->preActivation();
    }

    public function scopeBlockedActivated(Builder $query): Builder
    {
        return $query->blocked()->whereNotNull('activated_at');
    }

    public function scopeBlockedHolderLinked(Builder $query): Builder
    {
        return $query->blocked()->holderLinked();
    }

    public function scopeDraftHolderLinked(Builder $query): Builder
    {
        return $query->draft()->holderLinked();
    }

    public function scopeBlockedUnassigned(Builder $query): Builder
    {
        return $query->blocked()->unassigned();
    }

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
            'issued_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }
}
