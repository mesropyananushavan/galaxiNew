<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Notifications\Notifiable;

#[Fillable(['shop_id', 'name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function scopeBootstrapAdmins(Builder $query): Builder
    {
        return $query->whereNull('shop_id');
    }

    public function scopeAssignedToScopedShop(Builder $query): Builder
    {
        return $query->whereNotNull('shop_id');
    }

    public function scopeAssignedToActiveShop(Builder $query): Builder
    {
        return $query
            ->assignedToScopedShop()
            ->whereHas('shop', fn (Builder $shopQuery): Builder => $shopQuery->active());
    }

    public function scopeAssignedToPausedShop(Builder $query): Builder
    {
        return $query
            ->assignedToScopedShop()
            ->whereHas('shop', fn (Builder $shopQuery): Builder => $shopQuery->paused());
    }

    public function scopeRoleAssigned(Builder $query): Builder
    {
        return $query->whereHas('roles');
    }

    public function scopeRoleAssignedToScopedShop(Builder $query): Builder
    {
        return $query->roleAssigned()->assignedToScopedShop();
    }

    public function scopeRoleAssignedToActiveShop(Builder $query): Builder
    {
        return $query->roleAssignedToScopedShop()->assignedToActiveShop();
    }

    public function scopeRoleAssignedToPausedShop(Builder $query): Builder
    {
        return $query->roleAssignedToScopedShop()->assignedToPausedShop();
    }

    public function scopePermissionBearing(Builder $query): Builder
    {
        return $query->whereHas('roles', fn (Builder $roleQuery): Builder => $roleQuery->permissionBearing());
    }

    public function scopeShopScopedAdmins(Builder $query): Builder
    {
        return $query
            ->assignedToActiveShop()
            ->permissionBearing();
    }

    public function hasBootstrapAdminAccess(): bool
    {
        return $this->shop_id === null;
    }

    public function belongsToActiveShop(): bool
    {
        return $this->shop()->active()->exists();
    }

    public function hasPermissionBearingRole(): bool
    {
        return $this->roles()->permissionBearing()->exists();
    }

    public function hasShopScopedAdminAccess(): bool
    {
        if (! $this->belongsToActiveShop()) {
            return false;
        }

        return $this->hasPermissionBearingRole();
    }

    public function canAccessAdminPanel(): bool
    {
        if ($this->hasBootstrapAdminAccess()) {
            return true;
        }

        return $this->hasShopScopedAdminAccess();
    }

    public function canAccessShop(?Shop $shop): bool
    {
        if ($shop === null) {
            return false;
        }

        if ($this->hasBootstrapAdminAccess()) {
            return true;
        }

        if (! $this->hasShopScopedAdminAccess()) {
            return false;
        }

        return (int) $this->shop_id === (int) $shop->getKey();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
