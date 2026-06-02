<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'slug', 'is_active', 'review_note', 'access_note', 'assignment_note'])]
class Role extends Model
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

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    public function scopePermissionBearing(Builder $query): Builder
    {
        return $query->whereHas('permissions');
    }

    public function scopeActivePermissionBearing(Builder $query): Builder
    {
        return $query->active()->permissionBearing();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
