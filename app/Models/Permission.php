<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'review_note'])]
class Permission extends Model
{
    use HasFactory;

    public function scopeRoleLinked(Builder $query): Builder
    {
        return $query->whereHas('roles');
    }

    public function scopeAssignedToRoles(Builder $query): Builder
    {
        return $query->roleLinked();
    }

    public function scopeReviewNoted(Builder $query): Builder
    {
        return $query->whereNotNull('review_note')->where('review_note', '!=', '');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }
}
