<?php

use App\Http\Controllers\Admin\CardTypeStoreController;
use App\Http\Controllers\Admin\CardTypeToggleStatusController;
use App\Http\Controllers\Admin\CardTypeUpdateController;
use App\Http\Controllers\Admin\CardHolderStoreController;
use App\Http\Controllers\Admin\CardHolderUpdateController;
use App\Http\Controllers\Admin\CardStoreController;
use App\Http\Controllers\Admin\CardUpdateController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleStoreController;
use App\Http\Controllers\Admin\RoleUpdateController;
use App\Http\Controllers\Admin\ResourceIndexController;
use App\Http\Controllers\Admin\ShopStoreController;
use App\Http\Controllers\Admin\ShopUpdateController;
use App\Models\Card;
use App\Models\CardHolder;
use App\Models\CardType;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Shop;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['auth', 'can:access-admin'])
    ->as('admin.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/shops', ResourceIndexController::class)
            ->middleware('can:viewAny,'.Shop::class)
            ->defaults('resource', 'shops')
            ->name('shops.index');
        Route::post('/shops', ShopStoreController::class)
            ->middleware('can:create,'.Shop::class)
            ->name('shops.store');
        Route::patch('/shops/{shop}', ShopUpdateController::class)
            ->middleware('can:update,shop')
            ->name('shops.update');
        Route::get('/cardholders', ResourceIndexController::class)
            ->middleware('can:viewAny,'.CardHolder::class)
            ->defaults('resource', 'cardholders')
            ->name('cardholders.index');
        Route::post('/cardholders', CardHolderStoreController::class)
            ->middleware('can:create,'.CardHolder::class)
            ->name('cardholders.store');
        Route::patch('/cardholders/{cardholder}', CardHolderUpdateController::class)
            ->middleware('can:update,cardholder')
            ->name('cardholders.update');
        Route::get('/cards', ResourceIndexController::class)
            ->middleware('can:viewAny,'.Card::class)
            ->defaults('resource', 'cards')
            ->name('cards.index');
        Route::post('/cards', CardStoreController::class)
            ->middleware('can:create,'.Card::class)
            ->name('cards.store');
        Route::patch('/cards/{card}', CardUpdateController::class)
            ->middleware('can:update,card')
            ->name('cards.update');
        Route::get('/checks-points', ResourceIndexController::class)->defaults('resource', 'checks-points')->name('checks-points.index');
        Route::get('/card-types', ResourceIndexController::class)
            ->middleware('can:viewAny,'.CardType::class)
            ->defaults('resource', 'card-types')
            ->name('card-types.index');
        Route::post('/card-types', CardTypeStoreController::class)
            ->middleware('can:create,'.CardType::class)
            ->name('card-types.store');
        Route::patch('/card-types/{cardType}', CardTypeUpdateController::class)
            ->middleware('can:update,cardType')
            ->name('card-types.update');
        Route::patch('/card-types/{cardType}/toggle-status', CardTypeToggleStatusController::class)
            ->middleware('can:update,cardType')
            ->name('card-types.toggle-status');
        Route::get('/services-rules', ResourceIndexController::class)->defaults('resource', 'services-rules')->name('services-rules.index');
        Route::get('/gifts', ResourceIndexController::class)->defaults('resource', 'gifts')->name('gifts.index');
        Route::get('/roles-permissions', ResourceIndexController::class)
            ->middleware(['can:viewAny,'.Role::class, 'can:viewAny,'.Permission::class])
            ->defaults('resource', 'roles-permissions')
            ->name('roles-permissions.index');
        Route::post('/roles-permissions', RoleStoreController::class)
            ->middleware('can:create,'.Role::class)
            ->name('roles-permissions.store');
        Route::patch('/roles-permissions/{role}', RoleUpdateController::class)
            ->middleware('can:update,role')
            ->name('roles-permissions.update');
        Route::get('/reports', ResourceIndexController::class)->defaults('resource', 'reports')->name('reports.index');
    });
