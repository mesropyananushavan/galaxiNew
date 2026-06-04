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

    public function scopeDraftPermissionBearing(Builder $query): Builder
    {
        return $query->draft()->permissionBearing();
    }

    public function scopeAssigned(Builder $query): Builder
    {
        return $query->whereHas('users');
    }

    public function scopeShopScopedAssigned(Builder $query): Builder
    {
        return $query->whereHas('users', fn (Builder $userQuery): Builder => $userQuery->whereNotNull('shop_id'));
    }

    public function scopeActiveShopScopedPermissionBearing(Builder $query): Builder
    {
        return $query->active()->permissionBearing()->shopScopedAssigned();
    }

    public function scopeActiveAssignedToActiveShopPermissionBearing(Builder $query): Builder
    {
        return $query->activeAssignedPermissionBearing()->whereHas('users', fn (Builder $userQuery): Builder => $userQuery->assignedToActiveShop());
    }

    public function scopeActiveAssignedToPausedShopPermissionBearing(Builder $query): Builder
    {
        return $query->activeAssignedPermissionBearing()->whereHas('users', fn (Builder $userQuery): Builder => $userQuery->assignedToPausedShop());
    }

    public function scopeActiveAssigned(Builder $query): Builder
    {
        return $query->active()->assigned();
    }

    public function scopeActiveAssignedPermissionBearing(Builder $query): Builder
    {
        return $query->activePermissionBearing()->whereHas('users');
    }

    public function scopeReviewNoted(Builder $query): Builder
    {
        return $query->whereNotNull('review_note')->where('review_note', '!=', '');
    }

    public function scopeAccessNoted(Builder $query): Builder
    {
        return $query->whereNotNull('access_note')->where('access_note', '!=', '');
    }

    public function scopeAssignmentNoted(Builder $query): Builder
    {
        return $query->whereNotNull('assignment_note')->where('assignment_note', '!=', '');
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
